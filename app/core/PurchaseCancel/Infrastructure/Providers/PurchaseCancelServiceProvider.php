<?php

namespace Core\PurchaseCancel\Infrastructure\Providers;

use Core\PurchaseCancel\Application\UseCases\CreatePurchaseCancel;
use Illuminate\Support\ServiceProvider;
use Core\PurchaseCancel\Domain\Repositories\PurchaseCancelRepositoryInterface;
use Core\PurchaseCancel\Infrastructure\Repositories\EloquentPurchaseCancelRepository;
use Core\PurchaseCancel\Domain\Services\PurchaseCancelService;
use Core\PurchaseCancel\Infrastructure\Listeners\PurchaseCancelListeners;
use Core\PurchaseCancel\Infrastructure\Services\PurchaseCancelServiceImpl;

class PurchaseCancelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PurchaseCancelRepositoryInterface::class, EloquentPurchaseCancelRepository::class);
        $this->app->bind(PurchaseCancelService::class, PurchaseCancelServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(PurchaseCancelListeners $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('PurchaseCancel') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('PurchaseCancel'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('PurchaseCancel'));
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