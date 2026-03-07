<?php

namespace Core\Extension\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Extension\Domain\Repositories\ExtensionRepositoryInterface;
use Core\Extension\Infrastructure\Repositories\EloquentExtensionRepository;
use Core\Extension\Domain\Services\ExtensionService;
use Core\Extension\Domain\Supports\ExtensionInstall;
use Core\Extension\Domain\Supports\ExtensionInstallExecutor;
use Core\Extension\Http\Middlewares\BlockExtension;
use Core\Extension\Infrastructure\Services\ExtensionServiceImpl;
use Core\Extension\Infrastructure\Supports\ExtensionInstallExecutorImpl;
use Core\Extension\Infrastructure\Supports\ExtensionInstallImpl;
use Illuminate\Routing\Router;

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

    public function boot(Router $router)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $router->aliasMiddleware('extension.block', BlockExtension::class);
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
}