<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Reviews\Controllers\Api\ReviewClientController;
use App\Modules\Reviews\Controllers\Api\ReviewPublicController;

Route::prefix('api')->middleware('api')->group(function () {
    // client creates review
    Route::middleware(['auth:sanctum','role:client'])->group(function () {
        Route::post('client/reviews', [ReviewClientController::class, 'store']);
    });

    // public listing
    Route::get('lawyer/{lawyer}/reviews', [ReviewPublicController::class, 'index']);
});
