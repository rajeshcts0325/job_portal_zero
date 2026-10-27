<?php

use App\Http\Controllers\Api\PublicAuthController;
use App\Http\Middleware\AuthenticatePublicUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/public')->group(function () {
    Route::post('/login', [PublicAuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::post('/login/otp/request', [PublicAuthController::class, 'requestOtp'])
        ->middleware('throttle:3,1');

    Route::post('/login/otp/verify', [PublicAuthController::class, 'verifyOtp'])
        ->middleware('throttle:5,1');

    Route::middleware(AuthenticatePublicUser::class)->group(function () {
        Route::get('/me', function (Request $request) {
            $user = $request->user();

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'user_id' => $user->user_id,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ]);
        });

        Route::post('/logout', [PublicAuthController::class, 'logout']);
    });
});
