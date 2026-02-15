<?php

namespace Extensions\InvoiceOutPDF;

use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->tag(
            \Extensions\InvoiceOutPDF\Hooks\IndexShowHook::class,
            'liteerp.hooks'
        );
        $this->app->tag(
            \Extensions\InvoiceOutPDF\Hooks\IndexHook::class,
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
        $this->loadViewsFrom(__DIR__.'/Views','InvoiceOutPDF');
        $this->loadTranslationsFrom(__DIR__.'/lang','invoiceoutpdf');
    }
}
