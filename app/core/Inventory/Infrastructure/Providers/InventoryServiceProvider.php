<?php

namespace Core\Inventory\Infrastructure\Providers;

use Core\Inventory\Application\UseCases\OrderItemCompletedUpdate;
use Core\Inventory\Application\UseCases\AdjustmentUpdateInventory;
use Core\Inventory\Application\UseCases\OrderItemCancelledUpdate;
use Core\Inventory\Application\UseCases\UpdateInventoryById;
use Core\Inventory\Application\UseCases\UpdateInventoryByStockMovementIn;
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

    public function boot(
        UpdateInventoryById $UpdateInventoryById,
        UpdateInventoryByStockMovementIn $UpdateInventoryByStockMovementIn,
        OrderItemCompletedUpdate $OrderItemCompletedUpdate,
        AdjustmentUpdateInventory $AdjustmentUpdateInventory,
        OrderItemCancelledUpdate $OrderItemCancelledUpdate
    ) {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $this->loadModuleCommands();
        $listenr = new InventoryListener();
        $listenr->handle(
            $UpdateInventoryById,
            $UpdateInventoryByStockMovementIn,
            $OrderItemCompletedUpdate,
            $AdjustmentUpdateInventory,
            $OrderItemCancelledUpdate
        );
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
