<?php

namespace App\Http\Middleware;

use App\Models\PublicAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePublicUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'message' => 'Authentication required.',
            ], 401);
        }

        $token = PublicAccessToken::with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$token || !$token->user || !$token->user->isActive()) {
            return response()->json([
                'message' => 'Invalid or expired access token.',
            ], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        // Make the authenticated user available through $request->user().
        $request->setUserResolver(fn () => $token->user);

        return $next($request);
    }
}