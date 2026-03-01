<?php

namespace Extensions\CustomHomePage;

use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
        $this->app->tag(
            \Extensions\CustomHomePage\Hooks\CustomHomeHook::class,
            'liteerp.hooks'
        );
    }

    public function boot()
    {
        if(env('APP_ENV') !== 'production') {
          // support for development to easy
          $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        }
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadTranslationsFrom(__DIR__.'/lang','extension.customhomepage');
        $this->loadViewsFrom(__DIR__ . '/Resources/views','extension.customhomepage');
    }
}