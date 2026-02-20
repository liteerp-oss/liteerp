<?php

namespace Core\Permission\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Permission\Domain\Repositories\PermissionRepositoryInterface;
use Core\Permission\Infrastructure\Repositories\EloquentPermissionRepository;
use Core\Permission\Domain\Services\PermissionService;
use Core\Permission\Infrastructure\Listeners\ListenerEvent;
use Core\Permission\Infrastructure\Services\PermissionServiceImpl;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PermissionRepositoryInterface::class, EloquentPermissionRepository::class);
        $this->app->bind(PermissionService::class, PermissionServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(ListenerEvent $listenerEvent)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listenerEvent->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Permission') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Permission'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Permission'));
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

        if (is_dir(base_path('core'))) {
            $commandFiles = glob(base_path('core') . '/*/Console/*.php');

            if (!empty($commandFiles)) {
                foreach ($commandFiles as $file) {
                    require_once $file;
                }

                $commandClasses = array_map(function ($file) {
                    $class = basename($file, '.php');
                    $parts = explode(DIRECTORY_SEPARATOR, $file);
                    $moduleIndex = array_search('core', $parts);
                    $module = isset($parts[$moduleIndex + 1]) ? $parts[$moduleIndex + 1] : null;
                    return $module ? "Core\{$module}\Console\{$class}" : null;
                }, $commandFiles);

                $commandClasses = array_values(array_filter($commandClasses));

                if (!empty($commandClasses)) {
                    $this->commands($commandClasses);
                }
            }
        }
    }
}