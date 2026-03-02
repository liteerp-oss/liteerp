<?php

namespace Core\StockIn\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\StockIn\Domain\Repositories\StockInRepositoryInterface;
use Core\StockIn\Infrastructure\Repositories\EloquentStockInRepository;
use Core\StockIn\Domain\Services\StockInService;
use Core\StockIn\Infrastructure\Listeners\StockInListener;
use Core\StockIn\Infrastructure\Services\StockInServiceImpl;

class StockInServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StockInRepositoryInterface::class, EloquentStockInRepository::class);
        $this->app->bind(StockInService::class, StockInServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(StockInListener $listenr)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listenr->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('StockIn') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('StockIn'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('StockIn'));
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