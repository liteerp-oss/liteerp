<?php

namespace Core\Purchase\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Purchase\Domain\Repositories\PurchaseRepositoryInterface;
use Core\Purchase\Infrastructure\Repositories\EloquentPurchaseRepository;
use Core\Purchase\Domain\Services\PurchaseService;
use Core\Purchase\Infrastructure\Listeners\PurchaseListener;
use Core\Purchase\Infrastructure\Services\PurchaseServiceImpl;

class PurchaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PurchaseRepositoryInterface::class, EloquentPurchaseRepository::class);
        $this->app->bind(PurchaseService::class, PurchaseServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(PurchaseListener $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Purchase') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Purchase'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Purchase'));
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