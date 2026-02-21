<?php

namespace Extensions\FastMode;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
        $this->app->tag(
            \Extensions\FastMode\Hooks\TriggerUpdateInvoiceHook::class,
            'liteerp.hooks'
        );
        $this->app->tag(
            \Extensions\FastMode\Hooks\AddNavMenu::class,
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
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadTranslationsFrom(__DIR__.'/lang','extension.fastmode');
    }
}