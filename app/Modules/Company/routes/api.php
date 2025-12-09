<?php

use App\Modules\Company\Controllers\Api\CompanyLawyerController;
use App\Modules\Company\Controllers\Api\CompanyDirectoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/companies', [CompanyDirectoryController::class, 'index']);
    Route::get('/companies/{id}', [CompanyDirectoryController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role:company'])
    ->prefix('company')
    ->group(function () {
        // الشركة تشوف المحامين بتوعها
        Route::get('/lawyers', [CompanyLawyerController::class, 'index']);

        // ربط محامي بالشركة (assign)
        Route::post('/lawyers/attach', [CompanyLawyerController::class, 'attach']);

        // حذف محامي من الشركة (فك الربط)
        Route::delete('/lawyers/{lawyer}', [CompanyLawyerController::class, 'detach']);
        
        // Company availability CRUD endpoints
        Route::get('/availabilities', [\App\Modules\Company\Controllers\Api\CompanyAvailabilityController::class, 'index']);
        Route::post('/availabilities', [\App\Modules\Company\Controllers\Api\CompanyAvailabilityController::class, 'store']);
        Route::put('/availabilities/{id}', [\App\Modules\Company\Controllers\Api\CompanyAvailabilityController::class, 'update']);
        Route::delete('/availabilities/{id}', [\App\Modules\Company\Controllers\Api\CompanyAvailabilityController::class, 'destroy']);
        
        // Legacy upsert endpoint (kept for backward compatibility)
        Route::post('/availabilities/upsert', [\App\Modules\Company\Controllers\Api\CompanyAvailabilityUpsertController::class, 'upsert']);
    });
});
