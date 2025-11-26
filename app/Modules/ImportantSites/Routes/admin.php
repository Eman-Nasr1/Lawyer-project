<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ImportantSites\Controllers\Admin\ImportantSiteController;

Route::middleware(['web', 'auth.admin', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::resource('important-sites', ImportantSiteController::class)->except(['show']);
    });

