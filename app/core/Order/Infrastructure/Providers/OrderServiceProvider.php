<?php

namespace Core\Order\Infrastructure\Providers;

use Core\Order\Application\UseCases\CheckReadyForOrderItem;
use Core\Order\Application\UseCases\CheckOrderCancelled;
use Core\Order\Application\UseCases\CheckUpdateShippingOrder;
use Illuminate\Support\ServiceProvider;
use Core\Order\Domain\Repositories\OrderRepositoryInterface;
use Core\Order\Infrastructure\Repositories\EloquentOrderRepository;
use Core\Order\Domain\Services\OrderService;
use Core\Order\Infrastructure\Listeners\OrderListener;
use Core\Order\Infrastructure\Services\OrderServiceImpl;

class OrderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(OrderService::class, OrderServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(CheckReadyForOrderItem $CheckReadyForOrderItem,
        CheckUpdateShippingOrder $CheckUpdateShippingOrder,
        CheckOrderCancelled $CheckOrderCancelled)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener = new OrderListener();
        $listener->handle($CheckReadyForOrderItem,$CheckUpdateShippingOrder,$CheckOrderCancelled);
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Order') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('order'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('order'));
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