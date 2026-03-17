<?php

namespace Core\Inventory\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Inventory\Domain\Repositories\InventoryRepositoryInterface;
use Core\Inventory\Infrastructure\Repositories\EloquentInventoryRepository;
use Core\Inventory\Domain\Services\InventoryService;
use Core\Inventory\Infrastructure\Listeners\InventoryListener;
use Core\Inventory\Infrastructure\Services\InventoryServiceImpl;

class InventoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(InventoryRepositoryInterface::class, EloquentInventoryRepository::class);
        $this->app->bind(InventoryService::class, InventoryServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Inventory') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Inventory'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Inventory'));
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
