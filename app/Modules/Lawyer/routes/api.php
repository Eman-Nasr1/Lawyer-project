<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Lawyer\Controllers\Api\LawyerAvailabilityUpsertController;
use App\Modules\Lawyer\Controllers\Api\PublicAvailabilityController;

Route::middleware(['auth:sanctum','role:lawyer'])
    ->post('lawyer/availabilities/upsert', [LawyerAvailabilityUpsertController::class, 'upsert']);

// عام للعرض
Route::get('availabilities/slots', [PublicAvailabilityController::class, 'slots']);
