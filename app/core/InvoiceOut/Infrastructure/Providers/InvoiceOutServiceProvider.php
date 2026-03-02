<?php

namespace Core\InvoiceOut\Infrastructure\Providers;

use Core\InvoiceOut\Application\UseCases\CreateInvoiceOut;
use Core\InvoiceOut\Application\UseCases\UnapproveInvoiceOutByOrderCancelled;
use Core\InvoiceOut\Application\UseCases\UpdateTotalByShippingFee;
use Illuminate\Support\ServiceProvider;
use Core\InvoiceOut\Domain\Repositories\InvoiceOutRepositoryInterface;
use Core\InvoiceOut\Infrastructure\Repositories\EloquentInvoiceOutRepository;
use Core\InvoiceOut\Domain\Services\InvoiceOutService;
use Core\InvoiceOut\Infrastructure\Listeners\InvoiceOutListener;
use Core\InvoiceOut\Infrastructure\Services\InvoiceOutServiceImpl;

class InvoiceOutServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(InvoiceOutRepositoryInterface::class, EloquentInvoiceOutRepository::class);
        $this->app->bind(InvoiceOutService::class, InvoiceOutServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(InvoiceOutListener $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('InvoiceOut') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('InvoiceOut'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('InvoiceOut'));
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