<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Favorites\Controllers\Api\FavoriteController;

Route::prefix('api')->middleware('api')->group(function () {
    Route::middleware(['auth:sanctum','role:client'])->prefix('client')->group(function () {
        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::post('favorites/toggle', [FavoriteController::class, 'toggle']);
    });
});
