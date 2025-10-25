<?php

namespace App\Modules\Lawyer\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Lawyer\Repositories\LawyerAvailabilityRepositoryInterface;
use App\Modules\Lawyer\Repositories\Eloquent\LawyerAvailabilityRepository;

class LawyerAvailabilitiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // الربط بين الـ Interface و الـ Eloquent Repository
        $this->app->bind(
            LawyerAvailabilityRepositoryInterface::class,
            LawyerAvailabilityRepository::class
        );
    }

    public function boot(): void
    {
        // تحميل الراوتات الخاصة بالموديول
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // لو هتضيف مستقبلاً فيوز أو ترجمات أو مايجريشن
        // $this->loadViewsFrom(__DIR__.'/../Resources/views', 'lawyer_availabilities');
        // $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
    }
}
