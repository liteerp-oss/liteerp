<?php

namespace Core\StockOut\Infrastructure\Providers;

use Core\StockOut\Application\UseCases\CancelledStockOutByOrderCancelled;
use Core\StockOut\Infrastructure\Listeners\StockOutListener;
use Core\StockOut\Application\UseCases\CreateStockOut;
use Illuminate\Support\ServiceProvider;
use Core\StockOut\Domain\Repositories\StockOutRepositoryInterface;
use Core\StockOut\Infrastructure\Repositories\EloquentStockOutRepository;
use Core\StockOut\Domain\Services\StockOutService;
use Core\StockOut\Infrastructure\Services\StockOutServiceImpl;

class StockOutServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StockOutRepositoryInterface::class, EloquentStockOutRepository::class);
        $this->app->bind(StockOutService::class, StockOutServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(StockOutListener $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('StockOut') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('StockOut'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('StockOut'));
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