<?php

namespace Core\Notifications\Infrastructure\Providers;

use Core\Notifications\Application\UseCases\CreateNotification;
use Illuminate\Support\ServiceProvider;
use Core\Notifications\Domain\Repositories\NotificationRepositoryInterface;
use Core\Notifications\Infrastructure\Repositories\EloquentNotificationRepository;
use Core\Notifications\Domain\Services\NotificationDBService;
use Core\Notifications\Infrastructure\Listeners\NotificationWrite;
use Core\Notifications\Infrastructure\Services\NotificationDBServiceImpl;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
        $this->app->bind(NotificationDBService::class, NotificationDBServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(NotificationWrite $listener)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener->handle();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Notifications') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Notifications'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Notifications'));
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
