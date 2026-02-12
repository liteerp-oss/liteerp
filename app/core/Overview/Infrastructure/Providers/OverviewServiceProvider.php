<?php

namespace Core\Overview\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Overview\Domain\Repositories\OverviewRepositoryInterface;
use Core\Overview\Infrastructure\Repositories\EloquentOverviewRepository;
use Core\Overview\Domain\Services\OverviewService;
use Core\Overview\Infrastructure\Commands\OverviewCommand;
use Core\Overview\Infrastructure\Services\OverviewServiceImpl;
use Illuminate\Console\Scheduling\Schedule;
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
            $this->commands([
                OverviewCommand::class
            ]);
        }
        $this->app->booted(function () {
            app(Schedule::class)
                ->command('app:overview')
                ->everyThirtyMinutes()
                ->withoutOverlapping()
                ->onOneServer();
        });
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
}