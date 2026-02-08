<?php

namespace Core\Overview\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Overview\Domain\Repositories\OverviewRepositoryInterface;
use Core\Overview\Infrastructure\Repositories\EloquentOverviewRepository;
use Core\Overview\Domain\Services\OverviewService;
use Core\Overview\Infrastructure\Services\OverviewServiceImpl;

class OverviewServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OverviewRepositoryInterface::class, EloquentOverviewRepository::class);
        $this->app->bind(OverviewService::class, OverviewServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        if ($this->app->runningInConsole()) {
            $this->loadModuleCommands();
        }
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Overview') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Overview'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Overview'));
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

                $parts = explode('\\', __NAMESPACE__);
                array_pop($parts);
                array_pop($parts);
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
