<?php

namespace App\Providers;

use App\Domains\Admin\Plants\Contracts\PlantRepositoryInterface;
use App\Infrastructure\Repositories\EloquentPlantRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlantRepositoryInterface::class, EloquentPlantRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
