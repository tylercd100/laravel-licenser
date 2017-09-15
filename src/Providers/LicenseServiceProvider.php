<?php

namespace Tylercd100\License\Providers;

use Illuminate\Support\ServiceProvider;

class LicenseServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (method_exists($this, 'loadMigrationsFrom')) {
            $this->loadMigrationsFrom(__DIR__.'/../../migrations');
        } else {
            $this->publishes([
                __DIR__.'/../../migrations/' => database_path('migrations')
            ], 'migrations');
        }
    }
}