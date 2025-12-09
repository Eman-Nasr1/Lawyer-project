<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Lawyer\Controllers\Api\LawyerAvailabilityUpsertController;
use App\Modules\Lawyer\Controllers\Api\PublicAvailabilityController;
use App\Modules\Lawyer\Controllers\Api\LawyerDirectoryController;

Route::middleware(['auth:sanctum','role:lawyer'])->prefix('lawyer')->group(function () {
    // Availability CRUD endpoints
    Route::get('availabilities', [\App\Modules\Lawyer\Controllers\Api\LawyerAvailabilityController::class, 'index']);
    Route::post('availabilities', [\App\Modules\Lawyer\Controllers\Api\LawyerAvailabilityController::class, 'store']);
    Route::put('availabilities/{id}', [\App\Modules\Lawyer\Controllers\Api\LawyerAvailabilityController::class, 'update']);
    Route::delete('availabilities/{id}', [\App\Modules\Lawyer\Controllers\Api\LawyerAvailabilityController::class, 'destroy']);
    
    // Legacy upsert endpoint (kept for backward compatibility)
    Route::post('availabilities/upsert', [LawyerAvailabilityUpsertController::class, 'upsert']);
});

// عام للعرض
Route::get('availabilities/slots', [PublicAvailabilityController::class, 'slots']);

// Lawyer Directory APIs
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::get('lawyers', [LawyerDirectoryController::class, 'index']);
    Route::get('lawyers/{id}', [LawyerDirectoryController::class, 'show']);
});
