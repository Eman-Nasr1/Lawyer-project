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
        // نضمن إن الكنترولرز متشافة
        
    }
}
