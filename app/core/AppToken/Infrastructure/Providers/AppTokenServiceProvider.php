<?php

namespace Core\AppToken\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\AppToken\Domain\Repositories\AppTokenRepositoryInterface;
use Core\AppToken\Infrastructure\Repositories\EloquentAppTokenRepository;
use Core\AppToken\Domain\Services\AppTokenService;
use Core\AppToken\Infrastructure\Services\AppTokenServiceImpl;

class AppTokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AppTokenRepositoryInterface::class, EloquentAppTokenRepository::class);
        $this->app->bind(AppTokenService::class, AppTokenServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('AppToken') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('AppToken'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('AppToken'));
        }
    }

    protected function loadModuleRoutes(): void
    {
        $routePath = __DIR__ . '/../routes';
        if (file_exists("$routePath/api.php")) {
            $this->loadRoutesFrom("$routePath/api.php");
        }
        if (file_exists("$routePath/web.php")) {
            $this->loadRoutesFrom("$routePath/web.php");
        }
    }
}