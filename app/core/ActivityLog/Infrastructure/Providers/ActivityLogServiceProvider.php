<?php

namespace Core\ActivityLog\Infrastructure\Providers;

use Core\ActivityLog\Application\UseCases\CreateActivityLog;
use Illuminate\Support\ServiceProvider;
use Core\ActivityLog\Domain\Repositories\ActivityLogRepositoryInterface;
use Core\ActivityLog\Infrastructure\Repositories\EloquentActivityLogRepository;
use Core\ActivityLog\Domain\Services\ActivityLogService;
use Core\ActivityLog\Infrastructure\Listeners\ActivityLogWrite;
use Core\ActivityLog\Infrastructure\Services\ActivityLogServiceImpl;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ActivityLogRepositoryInterface::class, EloquentActivityLogRepository::class);
        $this->app->bind(ActivityLogService::class, ActivityLogServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(CreateActivityLog $createLog)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener = new ActivityLogWrite($createLog);
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('ActivityLog') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('ActivityLog'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('ActivityLog'));
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
