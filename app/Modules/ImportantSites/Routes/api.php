<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ImportantSites\Controllers\Api\ImportantSiteApiController;

Route::prefix('api')->group(function () {
    Route::get('important-sites', [ImportantSiteApiController::class, 'index']);
});

