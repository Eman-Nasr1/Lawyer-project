<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Addresses\Controllers\Api\LawyerAddressController;
use App\Modules\Addresses\Controllers\Api\CompanyAddressController;

Route::prefix('api')->middleware('api')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Lawyer addresses routes
        Route::prefix('lawyer')->group(function () {
            Route::get('addresses', [LawyerAddressController::class, 'index']);
            Route::post('addresses', [LawyerAddressController::class, 'store']);
            Route::get('addresses/{id}', [LawyerAddressController::class, 'show']);
            Route::match(['put', 'patch'], 'addresses/{id}', [LawyerAddressController::class, 'update']);
            Route::delete('addresses/{id}', [LawyerAddressController::class, 'destroy']);
            Route::post('addresses/{id}/set-primary', [LawyerAddressController::class, 'setPrimary']);
        });

        // Company addresses routes
        Route::prefix('company')->group(function () {
            Route::get('addresses', [CompanyAddressController::class, 'index']);
            Route::post('addresses', [CompanyAddressController::class, 'store']);
            Route::get('addresses/{id}', [CompanyAddressController::class, 'show']);
            Route::match(['put', 'patch'], 'addresses/{id}', [CompanyAddressController::class, 'update']);
            Route::delete('addresses/{id}', [CompanyAddressController::class, 'destroy']);
            Route::post('addresses/{id}/set-primary', [CompanyAddressController::class, 'setPrimary']);
        });
    });
});

