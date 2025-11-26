<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sliders\Controllers\Admin\SliderController;

Route::middleware(['web', 'auth.admin', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::resource('sliders', SliderController::class)->except(['show']);
    });

