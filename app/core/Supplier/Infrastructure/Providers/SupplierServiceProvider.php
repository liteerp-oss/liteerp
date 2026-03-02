<?php

namespace Core\Supplier\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Supplier\Domain\Repositories\SupplierRepositoryInterface;
use Core\Supplier\Infrastructure\Repositories\EloquentSupplierRepository;
use Core\Supplier\Domain\Services\SupplierService;
use Core\Supplier\Infrastructure\Services\SupplierServiceImpl;

class SupplierServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SupplierRepositoryInterface::class, EloquentSupplierRepository::class);
        $this->app->bind(SupplierService::class, SupplierServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Supplier') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Supplier'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Supplier'));
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