<?php

namespace Core\Notifications\Infrastructure\Providers;

use Core\Notifications\Application\UseCases\CreateNotification;
use Illuminate\Support\ServiceProvider;
use Core\Notifications\Domain\Repositories\NotificationRepositoryInterface;
use Core\Notifications\Infrastructure\Repositories\EloquentNotificationRepository;
use Core\Notifications\Domain\Services\NotificationDBService;
use Core\Notifications\Infrastructure\Listeners\NotificationWrite;
use Core\Notifications\Infrastructure\Services\NotificationDBServiceImpl;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
        $this->app->bind(NotificationDBService::class, NotificationDBServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(CreateNotification $CreateNotification)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener = new NotificationWrite();
        $listener->handle($CreateNotification);
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
