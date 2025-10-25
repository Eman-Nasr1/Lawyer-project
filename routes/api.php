<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordOtpController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('forgot-password', [PasswordOtpController::class, 'forgot']);
    Route::post('verify-otp',      [PasswordOtpController::class, 'verify']);
    Route::post('resend-otp',      [PasswordOtpController::class, 'resend']);
    Route::post('reset-password',  [PasswordOtpController::class, 'reset']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me',     [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// مثال على حماية أي APIs تانية:
Route::middleware('auth:sanctum')->get('/protected-check', function () {
    return ['ok' => true];
});
