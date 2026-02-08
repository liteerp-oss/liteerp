<?php

namespace Extensions\PusherSetting;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

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
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'PusherSetting');
        $this->configureBroadcasting();
    }

    protected function configureBroadcasting(): void
    {
        try {
            $envKey = env('PUSHER_APP_KEY');
            $envSecret = env('PUSHER_APP_SECRET');
            $envAppId = env('PUSHER_APP_ID');
            $envCluster = env('PUSHER_APP_CLUSTER');
            $envHost = env('PUSHER_HOST');
            $envPort = (int) env('PUSHER_PORT', 443);
            $envScheme = env('PUSHER_SCHEME', 'https');

            $source = ($envKey && $envSecret && $envAppId) ? [
                'key' => $envKey,
                'secret' => $envSecret,
                'app_id' => $envAppId,
                'cluster' => $envCluster,
                'host' => $envHost,
                'port' => $envPort,
                'scheme' => $envScheme,
            ] : null;

            if (!$source) {
                Config::set('broadcasting.default', 'null');
                return;
            }

            Config::set('broadcasting.default', 'pusher');
            Config::set('broadcasting.connections.pusher.key', $source['key']);
            Config::set('broadcasting.connections.pusher.secret', $source['secret']);
            Config::set('broadcasting.connections.pusher.app_id', $source['app_id']);
            Config::set('broadcasting.connections.pusher.options.cluster', $source['cluster']);
            Config::set('broadcasting.connections.pusher.options.host', $source['host']);
            Config::set('broadcasting.connections.pusher.options.port', (int) $source['port']);
            Config::set('broadcasting.connections.pusher.options.scheme', $source['scheme']);
            Config::set('broadcasting.connections.pusher.options.useTLS', ($source['scheme'] ?? 'https') === 'https');
        } catch (\Throwable $e) {
        }
    }
}
