<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Appointments\Controllers\Api\AppointmentClientController;
use App\Modules\Appointments\Controllers\Api\AppointmentLawyerController;

Route::prefix('api')->middleware('api')->group(function () {

    // العميل
    Route::middleware(['auth:sanctum', 'role:client'])->prefix('client')->group(function () {
        Route::get('appointments', [AppointmentClientController::class, 'index']);
        Route::post('appointments', [AppointmentClientController::class, 'store']); // حجز جديد مع ملفات

        // إلغاء ميعاد + سبب
        Route::post('appointments/{id}/cancel', [AppointmentClientController::class, 'cancel']);
        Route::delete('appointments/{id}', [AppointmentClientController::class, 'destroy']); // إلغاءه (soft delete)
    });

    // المحامي
    Route::middleware(['auth:sanctum', 'role:lawyer'])->prefix('lawyer')->group(function () {
        Route::get('appointments', [AppointmentLawyerController::class, 'index']);
        Route::put('appointments/{id}/status', [AppointmentLawyerController::class, 'updateStatus']); // confirm/cancel/complete
    });
});
