<?php

namespace Core\PermissionGroupUser\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\PermissionGroupUser\Domain\Repositories\PermissionGroupUserRepositoryInterface;
use Core\PermissionGroupUser\Infrastructure\Repositories\EloquentPermissionGroupUserRepository;
use Core\PermissionGroupUser\Domain\Services\PermissionGroupUserService;
use Core\PermissionGroupUser\Infrastructure\Listeners\PermissionGroupUserListener;
use Core\PermissionGroupUser\Infrastructure\Services\PermissionGroupUserServiceImpl;

class PermissionGroupUserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PermissionGroupUserRepositoryInterface::class, EloquentPermissionGroupUserRepository::class);
        $this->app->bind(PermissionGroupUserService::class, PermissionGroupUserServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(PermissionGroupUserListener $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('PermissionGroupUser') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('PermissionGroupUser'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('PermissionGroupUser'));
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