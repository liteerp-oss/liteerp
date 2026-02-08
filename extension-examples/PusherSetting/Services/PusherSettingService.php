<?php

namespace Extensions\PusherSetting\Services;

use Illuminate\Support\Facades\Process;

class PusherSettingService
{
    public function show(): array
    {
        return [
            'app_id' => env('PUSHER_APP_ID'),
            'app_key' => env('PUSHER_APP_KEY'),
            'app_secret' => env('PUSHER_APP_SECRET'),
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'host' => env('PUSHER_HOST'),
            'port' => (int) env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'enabled' => config('broadcasting.default') === 'pusher'
        ];
    }

    public function save(array $data): array
    {
        $this->writeEnv($data);
        $this->buildFrontend();
        return $data;
    }

    protected function writeEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }
        if (!is_writable($envPath)) {
            return;
        }
        $env = file_get_contents($envPath);
        $pairs = [
            'PUSHER_APP_ID' => $data['app_id'] ?? '',
            'PUSHER_APP_KEY' => $data['app_key'] ?? '',
            'PUSHER_APP_SECRET' => $data['app_secret'] ?? '',
            'PUSHER_APP_CLUSTER' => $data['cluster'] ?? '',
            'PUSHER_HOST' => $data['host'] ?? '',
            'PUSHER_PORT' => (string) ($data['port'] ?? '443'),
            'PUSHER_SCHEME' => $data['scheme'] ?? 'https',
            'BROADCAST_CONNECTION' => ($data['enabled'] ?? true) ? 'pusher' : config('broadcasting.default')
        ];
        foreach ($pairs as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, "{$key}={$value}", $env);
            } else {
                $env .= PHP_EOL . "{$key}={$value}";
            }
        }
        try {
            file_put_contents($envPath, $env);
        } catch (\Throwable $e) {
        }
    }

    protected function buildFrontend(): void
    {
        try {
            Process::run('php artisan app:npmbuild');
        } catch (\Throwable $e) {
        }
    }
}
