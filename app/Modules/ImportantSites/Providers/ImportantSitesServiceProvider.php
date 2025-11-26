<?php

namespace App\Modules\ImportantSites\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\ImportantSites\Repositories\ImportantSiteRepositoryInterface;
use App\Modules\ImportantSites\Repositories\Eloquent\ImportantSiteRepository;

class ImportantSitesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ImportantSiteRepositoryInterface::class, ImportantSiteRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}

