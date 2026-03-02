<?php

namespace Core\ImageManager\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Core\ImageManager\Domain\Repositories\ImageManagerRepositoryInterface;
use Core\ImageManager\Infrastructure\Repositories\EloquentImageManagerRepository;
use Core\ImageManager\Domain\Services\ImageManagerService;
use Core\ImageManager\Infrastructure\Services\ImageManagerServiceImpl;

class ImageManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ImageManagerRepositoryInterface::class, EloquentImageManagerRepository::class);
        $this->app->bind(ImageManagerService::class, ImageManagerServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot()
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('ImageManager') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('ImageManager'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('ImageManager'));
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