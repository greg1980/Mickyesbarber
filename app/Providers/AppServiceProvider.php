<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (App::environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        // Allow HTTP in local development
        if (App::environment('local')) {
            URL::forceScheme('http');
        }
    }
}
