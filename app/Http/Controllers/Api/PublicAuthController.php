<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpVerificationService;
use App\Services\PublicTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicAuthController extends Controller
{
    /**
     * Sign in with User ID and password.
     */
    public function login(Request $request, PublicTokenService $tokens): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        $user = User::where('user_id', $validated['user_id'])->first();

        // Use the same error for unknown users and incorrect passwords.
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => 'The supplied credentials are incorrect.',
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'This account is not active. Verify both contact methods or contact support.',
            ], 403);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_id' => $user->user_id,
            ],
            'token' => $tokens->issue($user),
        ]);
    }

    /**
     * Request a login code by email or SMS.
     */
    public function requestOtp(
        Request $request,
        OtpVerificationService $otpService
    ): JsonResponse {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'channel' => ['required', Rule::in(['email', 'sms'])],
        ]);

        $identifier = trim($validated['identifier']);
        $channel = $validated['channel'];

        $query = User::query()
            ->where('status', 'active');

        if ($channel === 'email') {
            $query->where('email', mb_strtolower($identifier))
                ->whereNotNull('email_verified_at');
        } else {
            $query->where('mobile', $identifier)
                ->whereNotNull('phone_verified_at');
        }

        $user = $query->first();

        // Don't reveal whether a particular email or phone is registered.
        if ($user) {
            try {
                $otpService->issue($user, $channel, 'login');
            } catch (\Throwable $exception) {
                // Record operational details privately, never the OTP.
                report($exception);
            }
        }

        return response()->json([
            'message' => 'If the active account and verified contact match, a login code will be sent shortly.',
        ], 202);
    }

    /**
     * Verify a login code and issue a bearer token.
     */
    public function verifyOtp(
        Request $request,
        OtpVerificationService $otpService,
        PublicTokenService $tokens
    ): JsonResponse {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'channel' => ['required', Rule::in(['email', 'sms'])],
            'otp' => ['required', 'digits:6'],
        ]);

        $identifier = trim($validated['identifier']);
        $channel = $validated['channel'];

        $query = User::query()
            ->where('status', 'active');

        if ($channel === 'email') {
            $query->where('email', mb_strtolower($identifier))
                ->whereNotNull('email_verified_at');
        } else {
            $query->where('mobile', $identifier)
                ->whereNotNull('phone_verified_at');
        }

        $user = $query->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => 'The code is invalid or expired.',
            ]);
        }

        // Failed verification attempts are persisted by the OTP service.
        try {
            $otpService->verify($user, $channel, $validated['otp'], 'login');
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages([
                'otp' => 'The code is invalid or expired. Request a new code if necessary.',
            ]);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_id' => $user->user_id,
            ],
            'token' => $tokens->issue($user),
        ]);
    }

    /**
     * Revoke the current bearer token.
     */
    public function logout(
        Request $request,
        PublicTokenService $tokens
    ): JsonResponse {
        $plainToken = $request->bearerToken();

        if ($plainToken) {
            $tokens->revoke($plainToken);
        }

        return response()->json([
            'message' => 'You have been signed out.',
        ]);
    }
}
