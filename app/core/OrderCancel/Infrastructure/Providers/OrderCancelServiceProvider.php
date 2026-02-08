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

    public function boot(CreateOrderCancel $CreateOrderCancel)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener = new OrderCancelListeners();
        $listener->handle($CreateOrderCancel);
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

    protected function loadModuleCommands(): void
    {
        $commandDir = __DIR__ . '/../../Console';
        if (is_dir($commandDir)) {
            $commandFiles = glob($commandDir . '/*.php');

            if (!empty($commandFiles)) {
                foreach ($commandFiles as $file) {
                    require_once $file;
                }

                // Get current namespace: Core\Module\Infrastructure\Providers
                $parts = explode('\\', __NAMESPACE__);
                // Remove Infrastructure and Providers (last 2)
                array_pop($parts);
                array_pop($parts);
                // Add Console
                $parts[] = 'Console';
                $consoleNamespace = implode('\\', $parts);

                $commandClasses = array_map(function ($file) use ($consoleNamespace) {
                    $class = basename($file, '.php');
                    return $consoleNamespace . "\\" . $class;
                }, $commandFiles);

                $commandClasses = array_values(array_filter($commandClasses));

                if (!empty($commandClasses)) {
                    $this->commands($commandClasses);
                }
            }
        }
    }
}
