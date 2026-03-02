<?php

namespace Core\CustomInvoiceIn\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\CustomInvoiceIn\Domain\Repositories\CustomInvoiceInRepositoryInterface;
use Core\CustomInvoiceIn\Infrastructure\Repositories\EloquentCustomInvoiceInRepository;
use Core\CustomInvoiceIn\Domain\Services\CustomInvoiceInService;
use Core\CustomInvoiceIn\Infrastructure\Services\CustomInvoiceInServiceImpl;

class CustomInvoiceInServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CustomInvoiceInRepositoryInterface::class, EloquentCustomInvoiceInRepository::class);
        $this->app->bind(CustomInvoiceInService::class, CustomInvoiceInServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('CustomInvoiceIn') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('CustomInvoiceIn'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('CustomInvoiceIn'));
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