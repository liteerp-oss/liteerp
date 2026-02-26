<?php

namespace Core\OrderItem\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\OrderItem\Domain\Repositories\OrderItemRepositoryInterface;
use Core\OrderItem\Infrastructure\Repositories\EloquentOrderItemRepository;
use Core\OrderItem\Domain\Services\OrderItemService;
use Core\OrderItem\Infrastructure\Listeners\OrderItemListener;
use Core\OrderItem\Infrastructure\Services\OrderItemServiceImpl;

class OrderItemServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderItemRepositoryInterface::class, EloquentOrderItemRepository::class);
        $this->app->bind(OrderItemService::class, OrderItemServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(OrderItemListener $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('OrderItem') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('OrderItem'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('OrderItem'));
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