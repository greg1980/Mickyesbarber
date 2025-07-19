<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add performance optimizations for production
        if ($this->app->environment('production')) {
            // Enable route caching
            if (!Route::getRoutes()->isEmpty()) {
                $this->app->make('router')->cache();
            }

            // Enable config caching
            $this->app->make('config')->cache();

            // Enable view caching
            $this->app->make('view')->cache();
        }
    }
}
