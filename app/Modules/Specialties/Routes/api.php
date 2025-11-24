<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Specialties\Controllers\Api\SpecialtyApiController;

Route::prefix('api')->group(function () {
    Route::get('specialties', [SpecialtyApiController::class, 'index']);
});
