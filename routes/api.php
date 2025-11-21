<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordOtpController;
use App\Http\Controllers\Api\SearchController;
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

// Search and Experience endpoints
Route::get('most-experienced-lawyers', [SearchController::class, 'mostExperiencedLawyers']);
Route::get('most-experienced-companies', [SearchController::class, 'mostExperiencedCompanies']);
Route::get('highest-rated-lawyers', [SearchController::class, 'highestRatedLawyers']);
Route::get('highest-rated-companies', [SearchController::class, 'highestRatedCompanies']);
Route::match(['get', 'post'], 'search', [SearchController::class, 'search']);

// Contact Us API
Route::post('contact-us', [\App\Http\Controllers\Api\ContactUsController::class, 'store']);

// مثال على حماية أي APIs تانية:
Route::middleware('auth:sanctum')->get('/protected-check', function () {
    return ['ok' => true];
});
