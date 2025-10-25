<?php
namespace App\Modules\Favorites\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Favorites\Repositories\FavoriteRepositoryInterface;
use App\Modules\Favorites\Repositories\Eloquent\FavoriteRepository;

class FavoritesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
    }
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
