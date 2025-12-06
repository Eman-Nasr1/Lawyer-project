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
    Route::post('resend-verification-otp', [AuthController::class, 'resendVerificationOtp']);
    Route::post('reset-password',  [PasswordOtpController::class, 'reset']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me',     [AuthController::class, 'me']);
        Route::match(['put', 'post'], 'profile', [AuthController::class, 'updateProfile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Search and Experience endpoints


// Dashboard APIs


// Public Dashboard APIs (no authentication required)

// Protected APIs (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('most-experienced-lawyers', [SearchController::class, 'mostExperiencedLawyers']);
    Route::get('most-experienced-companies', [SearchController::class, 'mostExperiencedCompanies']);
    Route::get('highest-rated-lawyers', [SearchController::class, 'highestRatedLawyers']);
    Route::get('highest-rated-companies', [SearchController::class, 'highestRatedCompanies']);
    Route::match(['get', 'post'], 'search', [SearchController::class, 'search']);

    // Contact Us API
    Route::post('contact-us', [\App\Http\Controllers\Api\ContactUsController::class, 'store']);
    Route::get('static-pages', [\App\Http\Controllers\Api\StaticPagesController::class, 'index']);
    Route::get('static-pages/{id}', [\App\Http\Controllers\Api\StaticPagesController::class, 'show']);
    Route::get('cities', [\App\Http\Controllers\Api\CitiesController::class, 'index']);
    Route::get('legal-decisions', [\App\Http\Controllers\Api\LegalDecisionsController::class, 'index']);
    Route::get('sliders', [\App\Modules\Sliders\Controllers\Api\SliderApiController::class, 'index']);
});

// مثال على حماية أي APIs تانية:
Route::middleware('auth:sanctum')->get('/protected-check', function () {
    return ['ok' => true];
});
