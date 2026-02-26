<?php

namespace Core\StockMovementIn\Infrastructure\Providers;

use Core\StockMovementIn\Application\UseCases\CompleteStockMovementIn;
use Illuminate\Support\ServiceProvider;
use Core\StockMovementIn\Domain\Repositories\StockMovementInRepositoryInterface;
use Core\StockMovementIn\Infrastructure\Repositories\EloquentStockMovementInRepository;
use Core\StockMovementIn\Domain\Services\StockMovementInService;
use Core\StockMovementIn\Infrastructure\Listeners\StockMovementInListener;
use Core\StockMovementIn\Infrastructure\Services\StockMovementInServiceImpl;

class StockMovementInServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StockMovementInRepositoryInterface::class, EloquentStockMovementInRepository::class);
        $this->app->bind(StockMovementInService::class, StockMovementInServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(StockMovementInListener $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('StockMovementIn') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('StockMovementIn'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('StockMovementIn'));
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