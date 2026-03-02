<?php

namespace Core\PermissionGroup\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\PermissionGroup\Domain\Repositories\PermissionGroupRepositoryInterface;
use Core\PermissionGroup\Infrastructure\Repositories\EloquentPermissionGroupRepository;
use Core\PermissionGroup\Domain\Services\PermissionGroupService;
use Core\PermissionGroup\Infrastructure\Listeners\PermissionGroupListener;
use Core\PermissionGroup\Infrastructure\Services\PermissionGroupServiceImpl;

class PermissionGroupServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PermissionGroupRepositoryInterface::class, EloquentPermissionGroupRepository::class);
        $this->app->bind(PermissionGroupService::class, PermissionGroupServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(PermissionGroupListener $permissionGroupListener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $permissionGroupListener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('PermissionGroup') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('PermissionGroup'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('PermissionGroup'));
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