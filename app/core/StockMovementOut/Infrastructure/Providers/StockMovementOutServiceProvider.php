<?php

namespace Core\StockMovementOut\Infrastructure\Providers;

use Core\StockMovementOut\Application\UseCases\CreateManyStockMovementOut;
use Illuminate\Support\ServiceProvider;
use Core\StockMovementOut\Domain\Repositories\StockMovementOutRepositoryInterface;
use Core\StockMovementOut\Infrastructure\Repositories\EloquentStockMovementOutRepository;
use Core\StockMovementOut\Domain\Services\StockMovementOutService;
use Core\StockMovementOut\Infrastructure\Listeners\StockMovementOutListener;
use Core\StockMovementOut\Infrastructure\Services\StockMovementOutServiceImpl;

class StockMovementOutServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StockMovementOutRepositoryInterface::class, EloquentStockMovementOutRepository::class);
        $this->app->bind(StockMovementOutService::class, StockMovementOutServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(StockMovementOutListener $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('StockMovementOut') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('StockMovementOut'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('StockMovementOut'));
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