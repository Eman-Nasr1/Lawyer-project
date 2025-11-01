<?php

namespace App\Modules\Addresses\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Addresses\Repositories\AddressRepositoryInterface;
use App\Modules\Addresses\Repositories\Eloquent\AddressRepository;

class AddressesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Binding: الـ Service يطلب Interface فياخد الـ Eloquent Repository
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
    }

    public function boot(): void
    {
        // تحميل راوتات الموديول
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}

