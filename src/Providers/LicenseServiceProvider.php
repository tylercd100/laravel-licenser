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
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');
    }
}