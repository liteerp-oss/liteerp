<?php

namespace Extensions\PusherSetting;

use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->tag(
            \Extensions\PusherSetting\Hooks\AddNavMenu::class,
            'liteerp.hooks'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        if (is_dir(__DIR__ . '/lang')) {
            $this->loadTranslationsFrom(__DIR__ . '/lang', 'PusherSetting');
        }
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'PusherSetting');
    }
}
