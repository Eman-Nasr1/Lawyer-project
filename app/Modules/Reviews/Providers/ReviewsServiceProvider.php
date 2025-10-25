<?php
namespace App\Modules\Reviews\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Reviews\Repositories\ReviewRepositoryInterface;
use App\Modules\Reviews\Repositories\Eloquent\ReviewRepository;

class ReviewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
    }
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
