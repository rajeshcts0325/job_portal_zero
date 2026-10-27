<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(
        Request $request,
        OtpVerificationService $otpService
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'mobile' => [
                'required',
                'string',
                'regex:/^\+[1-9]\d{7,14}$/',
                'unique:users,mobile',
            ],
            'user_id' => [
                'required',
                'string',
                'min:4',
                'max:30',
                'regex:/^[A-Za-z0-9._-]+$/',
                'unique:users,user_id',
            ],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'address' => ['nullable', 'string', 'max:1000'],
            'captcha' => ['required', 'string', 'max:2048'],
        ]);

        /*
         * Verify Cloudflare Turnstile CAPTCHA before creating the user.
         */
        $secret = config('services.turnstile.secret');

        if (!$secret) {
            report(new \RuntimeException('Turnstile secret is not configured.'));

            throw ValidationException::withMessages([
                'captcha' => 'Registration verification is temporarily unavailable.',
            ]);
        }

        try {
            $captchaResponse = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $validated['captcha'],
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'captcha' => 'CAPTCHA verification is temporarily unavailable. Please try again.',
            ]);
        }

        if (
            !$captchaResponse->successful()
            || !$captchaResponse->json('success')
        ) {
            throw ValidationException::withMessages([
                'captcha' => 'CAPTCHA verification failed. Please try again.',
            ]);
        }

        /*
         * Create the user only after successful CAPTCHA verification.
         */
        $user = DB::transaction(function () use ($validated) {
            return User::create([
                'name' => trim($validated['name']),
                'email' => mb_strtolower(trim($validated['email'])),
                'mobile' => $validated['mobile'],
                'user_id' => trim($validated['user_id']),
                'address' => $validated['address'] ?? null,
                'password' => $validated['password'],
                'status' => 'pending',
            ]);
        });

        /*
         * Issue email and SMS verification codes.
         */
        try {
            $otpService->issue($user, 'email', 'registration');
            $otpService->issue($user, 'sms', 'registration');
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'email' => 'Your account was created, but verification delivery could not be started. Please try resending the codes.',
            ]);
        }

        return redirect()
            ->route('register.verify.form', ['user_id' => $user->user_id])
            ->with(
                'status',
                'Your account was created. Check your email and phone for verification codes.'
            );
    }

    public function showVerification(Request $request): View
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string', 'max:30'],
        ]);

        $user = User::where('user_id', $validated['user_id'])->firstOrFail();

        return view('auth.verify-registration', [
            'userId' => $user->user_id,
            'emailVerified' => $user->email_verified_at !== null,
            'phoneVerified' => $user->phone_verified_at !== null,
        ]);
    }

    public function verify(
        Request $request,
        OtpVerificationService $otpService
    ): RedirectResponse {
        $validated = $request->validate([
            'user_id' => ['required', 'string', 'max:30'],
            'channel' => ['required', Rule::in(['email', 'sms'])],
            'otp' => ['required', 'digits:6'],
        ]);

        $user = User::where('user_id', $validated['user_id'])->firstOrFail();

        if ($user->status === 'suspended') {
            throw ValidationException::withMessages([
                'user_id' => 'This account is unavailable. Contact support.',
            ]);
        }

        if ($validated['channel'] === 'email' && $user->email_verified_at) {
            return back()->with('status', 'Your email is already verified.');
        }

        if ($validated['channel'] === 'sms' && $user->phone_verified_at) {
            return back()->with('status', 'Your phone number is already verified.');
        }

        $otpService->verify(
            $user,
            $validated['channel'],
            $validated['otp'],
            'registration'
        );

        DB::transaction(function () use ($user, $validated) {
            $lockedUser = User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['channel'] === 'email') {
                $lockedUser->email_verified_at ??= now();
            } else {
                $lockedUser->phone_verified_at ??= now();
            }

            if ($lockedUser->email_verified_at && $lockedUser->phone_verified_at) {
                $lockedUser->status = 'active';
            }

            $lockedUser->save();
        });

        $freshUser = $user->fresh();

        if ($freshUser->status === 'active') {
            return redirect()
                ->route('login')
                ->with('status', 'Both contact methods are verified. You can now sign in.');
        }

        return back()->with(
            'status',
            'Code verified. Verify your other contact method to activate your account.'
        );
    }

    public function resend(
        Request $request,
        OtpVerificationService $otpService
    ): RedirectResponse {
        $validated = $request->validate([
            'user_id' => ['required', 'string', 'max:30'],
            'channel' => ['required', Rule::in(['email', 'sms'])],
        ]);

        $user = User::where('user_id', $validated['user_id'])->firstOrFail();

        if ($user->status !== 'pending') {
            return back()->with(
                'status',
                'This account does not need registration verification.'
            );
        }

        if ($validated['channel'] === 'email' && $user->email_verified_at) {
            return back()->with('status', 'Your email is already verified.');
        }

        if ($validated['channel'] === 'sms' && $user->phone_verified_at) {
            return back()->with('status', 'Your phone number is already verified.');
        }

        $otpService->issue($user, $validated['channel'], 'registration');

        return back()->with(
            'status',
            'A new verification code has been queued for delivery.'
        );
    }
}