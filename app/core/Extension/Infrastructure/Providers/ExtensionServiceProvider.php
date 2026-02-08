<?php

namespace Core\Extension\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Extension\Domain\Repositories\ExtensionRepositoryInterface;
use Core\Extension\Infrastructure\Repositories\EloquentExtensionRepository;
use Core\Extension\Domain\Services\ExtensionService;
use Core\Extension\Domain\Supports\ExtensionInstall;
use Core\Extension\Domain\Supports\ExtensionInstallExecutor;
use Core\Extension\Infrastructure\Services\ExtensionServiceImpl;
use Core\Extension\Infrastructure\Supports\ExtensionInstallExecutorImpl;
use Core\Extension\Infrastructure\Supports\ExtensionInstallImpl;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ExtensionRepositoryInterface::class, EloquentExtensionRepository::class);
        $this->app->bind(ExtensionService::class, ExtensionServiceImpl::class);
        $this->app->bind(ExtensionInstall::class, ExtensionInstallImpl::class);
        $this->app->bind(ExtensionInstallExecutor::class, ExtensionInstallExecutorImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Extension') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Extension'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Extension'));
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
