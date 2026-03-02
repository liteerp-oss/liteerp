<?php

namespace Core\CustomInvoiceOut\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\CustomInvoiceOut\Domain\Repositories\CustomInvoiceOutRepositoryInterface;
use Core\CustomInvoiceOut\Infrastructure\Repositories\EloquentCustomInvoiceOutRepository;
use Core\CustomInvoiceOut\Domain\Services\CustomInvoiceOutService;
use Core\CustomInvoiceOut\Infrastructure\Services\CustomInvoiceOutServiceImpl;

class CustomInvoiceOutServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CustomInvoiceOutRepositoryInterface::class, EloquentCustomInvoiceOutRepository::class);
        $this->app->bind(CustomInvoiceOutService::class, CustomInvoiceOutServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('CustomInvoiceOut') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('CustomInvoiceOut'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('CustomInvoiceOut'));
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