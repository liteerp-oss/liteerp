<?php

namespace App\Providers;

use App\Contracts\Events\ExtensionEvent;
use App\Supports\Events\ExtensionEventImpl;
use App\Supports\Hooks\HookDispatcher;
use Core\Extension\Application\UseCases\AllExtension;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerCoreModules();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(AllExtension $allExtension): void
    {
        //
        $this->autoloadExtension($allExtension);
        $this->app->singleton(HookDispatcher::class);
        $this->app->bind(ExtensionEvent::class,ExtensionEventImpl::class);
    }
    protected function registerCoreModules(): void
    {
        $this->autoloadModule();
    }
    protected function autoloadModule()
    {
        $corePath = base_path('core');
        if (!File::exists($corePath)) {
            return;
        }
        $modules = File::directories($corePath);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $providerPath = "{$modulePath}/Infrastructure/Providers/{$moduleName}ServiceProvider.php";

            if (File::exists($providerPath)) {
                $providerClass = "Core\\{$moduleName}\\Infrastructure\\Providers\\{$moduleName}ServiceProvider";

                try {
                    $this->app->register($providerClass);
                } catch (\Throwable $e) {
                    logger()->error("Failed to register {$providerClass}: " . $e->getMessage());
                }
            }
        }
    }

    protected function autoloadExtension(AllExtension $allExtension): void
    {
        $extensionsPath = base_path('extensions');

        if (! File::exists($extensionsPath)) {
            return;
        }

        foreach ($allExtension->handle() as $extension) {
            $moduleName = $extension['name'];
            $extensionPath = $extension['directory'];
            $providerPath = base_path('extensions/' .$extensionPath . "/ExtensionServiceProvider.php");

            if (boolval($extension["status"]) === false) {
                continue;
            }

            if (! File::exists($providerPath)) {
                logger()->warning("Extension {$moduleName} enabled but ExtensionServiceProvider.php not found");
                continue;
            }

            $providerClass = "Extensions\\{$extensionPath}\\ExtensionServiceProvider";

            try {
                $this->app->register($providerClass);
            } catch (\Throwable $e) {
                logger()->error("Failed to register extension {$moduleName}: {$e->getMessage()}");
            }
        }
    }
}
