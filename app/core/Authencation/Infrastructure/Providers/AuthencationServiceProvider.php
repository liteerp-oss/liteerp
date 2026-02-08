<?php

namespace Core\Authencation\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Authencation\Domain\Repositories\AuthencationRepositoryInterface;
use Core\Authencation\Infrastructure\Repositories\EloquentAuthencationRepository;
use Core\Authencation\Domain\Services\AuthencationService;
use Core\Authencation\Domain\Services\AuthSessionManager;
use Core\Authencation\Infrastructure\Services\AuthencationServiceImpl;
use Core\Authencation\Infrastructure\Services\AuthSessionManagerImplement;

class AuthencationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthencationRepositoryInterface::class, EloquentAuthencationRepository::class);
        $this->app->bind(AuthencationService::class, AuthencationServiceImpl::class);
        $this->app->bind(AuthSessionManager::class, AuthSessionManagerImplement::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Authencation') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Authencation'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Authencation'));
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
