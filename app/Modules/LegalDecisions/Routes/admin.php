<?php

use Illuminate\Support\Facades\Route;
use App\Modules\LegalDecisions\Controllers\Admin\CategoryController;
use App\Modules\LegalDecisions\Controllers\Admin\DecisionController;

Route::middleware(['web','auth.admin','role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::prefix('legal')->name('legal.')->group(function () {
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('decisions',  DecisionController::class)->except(['show']);
        });
    });
