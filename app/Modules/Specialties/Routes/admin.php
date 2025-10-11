<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Specialties\Controllers\Admin\SpecialtyController;

Route::middleware(['web','auth.admin','role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::resource('specialties', SpecialtyController::class)->except(['show']);
    });
