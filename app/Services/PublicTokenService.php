<?php

namespace App\Services;

use App\Models\PublicAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicTokenService
{
    /**
     * Return the plain token once. Only its SHA-256 hash is stored.
     */
    public function issue(User $user): array
    {
        $plainToken = rtrim(
            strtr(base64_encode(random_bytes(48)), '+/', '-_'),
            '='
        );

        $record = DB::transaction(function () use ($user, $plainToken) {
            // Enforce one active public access token per user.
            PublicAccessToken::where('user_id', $user->id)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            return PublicAccessToken::create([
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $plainToken),
                'expires_at' => now()->addMinutes(15),
            ]);
        });

        return [
            'access_token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => $record->expires_at->toISOString(),
        ];
    }

    public function revoke(string $plainToken): void
    {
        PublicAccessToken::where(
            'token_hash',
            hash('sha256', $plainToken)
        )
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }
}