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
}