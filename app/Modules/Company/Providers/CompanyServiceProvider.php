<?php

namespace App\Modules\Company\Providers;

use Illuminate\Support\ServiceProvider;

class CompanyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

       
    }

    public function register()
    {
        // Register CompanyAvailabilityRepository binding
        $this->app->bind(
            \App\Modules\Company\Repositories\CompanyAvailabilityRepositoryInterface::class,
            \App\Modules\Company\Repositories\Eloquent\CompanyAvailabilityRepository::class
        );
    }
}
