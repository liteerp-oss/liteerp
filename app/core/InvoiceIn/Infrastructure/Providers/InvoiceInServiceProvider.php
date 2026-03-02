<?php

namespace Core\InvoiceIn\Infrastructure\Providers;

use Core\InvoiceIn\Application\UseCases\AutomaticCreateInvoice;
use Core\InvoiceIn\Application\UseCases\UnapprovedInvoiceIn;
use Illuminate\Support\ServiceProvider;
use Core\InvoiceIn\Domain\Repositories\InvoiceInRepositoryInterface;
use Core\InvoiceIn\Infrastructure\Repositories\EloquentInvoiceInRepository;
use Core\InvoiceIn\Domain\Services\InvoiceInService;
use Core\InvoiceIn\Infrastructure\Listeners\InvoiceInListener;
use Core\InvoiceIn\Infrastructure\Services\InvoiceInServiceImpl;

class InvoiceInServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(InvoiceInRepositoryInterface::class, EloquentInvoiceInRepository::class);
        $this->app->bind(InvoiceInService::class, InvoiceInServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(InvoiceInListener $invoiceInListener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $invoiceInListener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('InvoiceIn') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('InvoiceIn'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('InvoiceIn'));
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