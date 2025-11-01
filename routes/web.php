<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    // Guest routes (only accessible when not authenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('admin.register');
        Route::post('/register', [AuthController::class, 'register']);
    });

    // Authenticated admin routes
    Route::middleware(['auth.admin', 'role:admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');
    });
});
Route::middleware(['web','auth','role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
        
        // Lawyers Management
        Route::get('lawyers', [\App\Http\Controllers\Admin\LawyerController::class, 'index'])->name('lawyers.index');
        Route::get('lawyers/{lawyer}', [\App\Http\Controllers\Admin\LawyerController::class, 'show'])->name('lawyers.show');
        Route::put('lawyers/{lawyer}', [\App\Http\Controllers\Admin\LawyerController::class, 'update'])->name('lawyers.update');
        Route::put('lawyers/{lawyer}/approve', [\App\Http\Controllers\Admin\LawyerController::class, 'approve'])->name('lawyers.approve');
        Route::put('lawyers/{lawyer}/reject', [\App\Http\Controllers\Admin\LawyerController::class, 'reject'])->name('lawyers.reject');
        Route::delete('lawyers/{lawyer}', [\App\Http\Controllers\Admin\LawyerController::class, 'destroy'])->name('lawyers.destroy');
        
        // Companies Management
        Route::get('companies', [\App\Http\Controllers\Admin\CompanyController::class, 'index'])->name('companies.index');
        Route::get('companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'show'])->name('companies.show');
        Route::put('companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'update'])->name('companies.update');
        Route::put('companies/{company}/approve', [\App\Http\Controllers\Admin\CompanyController::class, 'approve'])->name('companies.approve');
        Route::put('companies/{company}/reject', [\App\Http\Controllers\Admin\CompanyController::class, 'reject'])->name('companies.reject');
        Route::delete('companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'destroy'])->name('companies.destroy');
    });