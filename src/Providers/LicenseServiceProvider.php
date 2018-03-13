<?php

namespace Tylercd100\License\Providers;

use Tylercd100\License\Commands\LicenseUpdate;
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
        $this->mergeConfigFrom(__DIR__.'/../../config/licenses.php', 'licenses');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Config
        $this->publishes([
            __DIR__.'/../config/licenses.php' => base_path('config/licenses.php'),
        ]);

        // Migrations
        if (method_exists($this, 'loadMigrationsFrom')) {
            $this->loadMigrationsFrom(__DIR__.'/../../migrations');
        } else {
            $this->publishes([
                __DIR__.'/../../migrations/' => database_path('migrations')
            ], 'migrations');
        }

        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                config('licenses.command_update'), // LicenseUpdate
            ]);
        }
    }
}