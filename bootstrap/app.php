<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Modules\Specialties\Providers\SpecialtiesServiceProvider::class,
        App\Modules\LegalDecisions\Providers\LegalDecisionsServiceProvider::class,
        App\Modules\Lawyer\Providers\LawyerAvailabilitiesServiceProvider::class,  
        App\Modules\Appointments\Providers\AppointmentsServiceProvider::class,
        App\Modules\Favorites\Providers\FavoritesServiceProvider::class,
        App\Modules\Reviews\Providers\ReviewsServiceProvider::class,
        App\Modules\Addresses\Providers\AddressesServiceProvider::class,
      
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'auth.admin' => \App\Http\Middleware\AdminAuthenticate::class,
            'admin.locale' => \App\Http\Middleware\SetAdminLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
