<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('db.connector.mysql', function () {
            return new \App\Database\Connectors\CustomMySqlConnector;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || strpos(config('app.url'), 'https') !== false) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
