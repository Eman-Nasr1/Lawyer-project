<?php

namespace App\Modules\Sliders\Providers;

use Illuminate\Support\ServiceProvider;

class SlidersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}

