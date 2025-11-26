<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Lawyer\Controllers\Api\LawyerAvailabilityUpsertController;
use App\Modules\Lawyer\Controllers\Api\PublicAvailabilityController;
use App\Modules\Lawyer\Controllers\Api\LawyerDirectoryController;

Route::middleware(['auth:sanctum','role:lawyer'])
    ->post('lawyer/availabilities/upsert', [LawyerAvailabilityUpsertController::class, 'upsert']);

// عام للعرض
Route::get('availabilities/slots', [PublicAvailabilityController::class, 'slots']);

// Lawyer Directory APIs
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::get('lawyers', [LawyerDirectoryController::class, 'index']);
    Route::get('lawyers/{id}', [LawyerDirectoryController::class, 'show']);
});
