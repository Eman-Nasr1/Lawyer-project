<?php

namespace App\Modules\Appointments\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Modules\Appointments\Repositories\Eloquent\AppointmentRepository;

class AppointmentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
    }

    public function boot(): void
    {
        // لو بتستخدم Laravel 11+ وعامل bootstrap/app.php، تقدر تكتفي بـ loadRoutesFrom
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }
}
