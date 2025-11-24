<?php

namespace App\Modules\Specialties\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Specialties\Repositories\SpecialtyRepositoryInterface;
use App\Modules\Specialties\Repositories\Eloquent\SpecialtyRepository;

class SpecialtiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Binding: الـ Service يطلب Interface فياخد الـ Eloquent Repository
        $this->app->bind(SpecialtyRepositoryInterface::class, SpecialtyRepository::class);
    }

    public function boot(): void
    {
        // تحميل راوتات الموديول
       
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        // لو فيه فيوز/ترجمة:
        // $this->loadViewsFrom(__DIR__.'/../Resources/views', 'specialties');
        // $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
    }
}
