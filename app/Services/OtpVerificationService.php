<?php

namespace App\Services;

use App\Jobs\SendOtpNotification;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpVerificationService
{
    public function issue(
        User $user,
        string $channel,
        string $purpose = 'registration'
    ): void {
        if (!in_array($channel, ['email', 'sms'], true)) {
            throw ValidationException::withMessages([
                'channel' => 'Invalid verification channel.',
            ]);
        }

        $latest = OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        $cooldown = config(
            'services.portal_auth.otp_cooldown_seconds',
            60
        );

        if (
            $latest?->last_sent_at &&
            $latest->last_sent_at->gt(now()->subSeconds($cooldown))
        ) {
            throw ValidationException::withMessages([
                'otp' => "Please wait {$cooldown} seconds before requesting another code.",
            ]);
        }

        $recipient = $channel === 'email'
            ? $user->email
            : $user->mobile;

        if (!$recipient) {
            throw ValidationException::withMessages([
                'otp' => 'No verified delivery destination is available.',
            ]);
        }

        $code = (string) random_int(100000, 999999);

        $verification = DB::transaction(function () use (
            $user,
            $channel,
            $purpose,
            $code
        ) {
            OtpVerification::where('user_id', $user->id)
                ->where('channel', $channel)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);

            return OtpVerification::create([
                'user_id' => $user->id,
                'channel' => $channel,
                'purpose' => $purpose,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(
                    config('services.portal_auth.otp_ttl_minutes', 5)
                ),
                'last_sent_at' => now(),
            ]);
        });

        SendOtpNotification::dispatch(
            $channel,
            $recipient,
            Crypt::encryptString($code),
            $purpose
        )->afterCommit();
    }

    public function verify(
        User $user,
        string $channel,
        string $code,
        string $purpose = 'registration'
    ): bool {
        $result = DB::transaction(function () use ($user, $channel, $code, $purpose) {
            $verification = OtpVerification::query()
                ->where('user_id', $user->id)
                ->where('channel', $channel)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$verification) {
                return ['ok' => false, 'message' => 'No active code exists. Request a new code.'];
            }

            if ($verification->expires_at->isPast()) {
                $verification->update(['consumed_at' => now()]);

                return ['ok' => false, 'message' => 'Your code has expired. Request a new code.'];
            }

            $maxAttempts = (int) config('services.portal_auth.otp_max_attempts', 5);

            if ($verification->attempts >= $maxAttempts) {
                $verification->update(['consumed_at' => now()]);

                return ['ok' => false, 'message' => 'Too many attempts. Request a new code.'];
            }

            if (!Hash::check($code, $verification->code_hash)) {
                $newAttempts = $verification->attempts + 1;

                $verification->update([
                    'attempts' => $newAttempts,
                    'consumed_at' => $newAttempts >= $maxAttempts ? now() : null,
                ]);

                return [
                    'ok' => false,
                    'message' => $newAttempts >= $maxAttempts
                        ? 'Too many incorrect attempts. Request a new code.'
                        : 'The verification code is incorrect.',
                ];
            }

            $verification->update(['consumed_at' => now()]);

            return ['ok' => true, 'message' => 'Code verified.'];
        });

        if (!$result['ok']) {
            throw ValidationException::withMessages([
                'otp' => $result['message'],
            ]);
        }

        return true;
    }
}
