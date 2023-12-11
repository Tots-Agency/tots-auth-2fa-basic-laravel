<?php

namespace Tots\AuthTfaBasic\Providers;

use Illuminate\Support\ServiceProvider;

class TfaBasicServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register migrations
        if($this->app->runningInConsole()){
            $this->registerMigrations();
        }
    }
    /**
     * Register migrations library
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $mainPath = database_path('migrations');
        $paths = array_merge([
            './vendor/tots/auth-2fa-basic-laravel/database/migrations'
        ], [$mainPath]);
        $this->loadMigrationsFrom($paths);
    }

    /**
     * Boot the blog services for the application.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
