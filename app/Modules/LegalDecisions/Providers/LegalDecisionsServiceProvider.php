<?php

namespace App\Modules\LegalDecisions\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\LegalDecisions\Repositories\CategoryRepositoryInterface;
use App\Modules\LegalDecisions\Repositories\Eloquent\CategoryRepository;
use App\Modules\LegalDecisions\Repositories\DecisionRepositoryInterface;
use App\Modules\LegalDecisions\Repositories\Eloquent\DecisionRepository;

class LegalDecisionsServiceProvider extends ServiceProvider
{
    /**
     * Register bindings and services.
     */
    public function register(): void
    {
        // 🧩 Binding the interfaces with their Eloquent implementations
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(DecisionRepositoryInterface::class, DecisionRepository::class);
    }

    /**
     * Bootstrap module routes, views, etc.
     */
    public function boot(): void
    {
        // 📜 تحميل الراوتات الخاصة بالأدمن
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');

        // 📜 تحميل راوتات الـ API لو موجودة
        $apiRoutes = __DIR__ . '/../Routes/api.php';
        if (file_exists($apiRoutes)) {
            $this->loadRoutesFrom($apiRoutes);
        }

        // 📄 تحميل الفيوهات من resources/views (الافتراضي)
        // لو حبيتي يكون للموديول فولدر فيوهات خاص:
        // ضيفي Resources/views جوه الموديول واستخدمي السطر التالى بدلًا من التعليق
        // $this->loadViewsFrom(__DIR__.'/../Resources/views', 'legal');

        // 🗂️ تحميل الترجمات أو المايجريشنز لو متوفرة (اختياري)
        // $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'legal');
        // $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
    }
}
