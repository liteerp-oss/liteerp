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
