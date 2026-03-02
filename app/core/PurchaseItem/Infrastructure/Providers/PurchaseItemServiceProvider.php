<?php

namespace Core\PurchaseItem\Infrastructure\Providers;

use Core\PurchaseItem\Application\UseCases\CheckForPurchaseRequested;
use Core\PurchaseItem\Application\UseCases\CheckForStockMovementIn;
use Illuminate\Support\ServiceProvider;
use Core\PurchaseItem\Domain\Repositories\PurchaseItemRepositoryInterface;
use Core\PurchaseItem\Infrastructure\Repositories\EloquentPurchaseItemRepository;
use Core\PurchaseItem\Domain\Services\PurchaseItemService;
use Core\PurchaseItem\Infrastructure\Listeners\PurchaseItemListner;
use Core\PurchaseItem\Infrastructure\Services\PurchaseItemServiceImpl;

class PurchaseItemServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PurchaseItemRepositoryInterface::class, EloquentPurchaseItemRepository::class);
        $this->app->bind(PurchaseItemService::class, PurchaseItemServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(PurchaseItemListner $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('PurchaseItem') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('PurchaseItem'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('PurchaseItem'));
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