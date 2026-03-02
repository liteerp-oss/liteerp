<?php

namespace Core\OrderCancel\Infrastructure\Providers;

use Core\OrderCancel\Application\UseCases\CreateOrderCancel;
use Illuminate\Support\ServiceProvider;
use Core\OrderCancel\Domain\Repositories\OrderCancelRepositoryInterface;
use Core\OrderCancel\Infrastructure\Repositories\EloquentOrderCancelRepository;
use Core\OrderCancel\Domain\Services\OrderCancelService;
use Core\OrderCancel\Infrastructure\Listeners\OrderCancelListeners;
use Core\OrderCancel\Infrastructure\Services\OrderCancelServiceImpl;

class OrderCancelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderCancelRepositoryInterface::class, EloquentOrderCancelRepository::class);
        $this->app->bind(OrderCancelService::class, OrderCancelServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(OrderCancelListeners $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('OrderCancel') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('OrderCancel'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('OrderCancel'));
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