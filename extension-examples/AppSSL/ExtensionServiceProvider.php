<?php

namespace Extensions\AppSSL;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        URL::forceScheme('https');
    }
}