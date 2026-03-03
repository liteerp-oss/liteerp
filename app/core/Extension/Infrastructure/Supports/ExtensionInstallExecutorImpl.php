<?php

namespace Core\Extension\Infrastructure\Supports;

use App\Exceptions\BadException;
use Core\Extension\Application\DTOs\ExtensionInstallPlan;
use Core\Extension\Domain\Supports\ExtensionInstallExecutor;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ExtensionInstallExecutorImpl implements ExtensionInstallExecutor
{
    private $allowCommands = [
        "app:npmbuild"
    ];
    public function execute(ExtensionInstallPlan $plan): void
    {

        $this->runMigrations($plan);
        $this->runCommands($plan);
        $this->publishAssets($plan);
        $this->log($plan);
    }
    private function runMigrations(ExtensionInstallPlan $plan): void
    {
        foreach ($plan->migrations as $migration) {
            $filePath = "extensions/" . $plan->directory . "/Database/Migrations/" . $migration;
            if (file_exists(base_path($filePath))) {
                Artisan::call($plan->install ? "migrate" : "migrate:rollback", [
                    '--path' => $filePath,
                    '--force' => true
                ]);
            } else {
                throw new BadException(__("extension::messages.migration_not_found", ['migration' => $migration]));
            }
        }
    }
    private function runCommands(ExtensionInstallPlan $plan): void
    {
        foreach ($plan->commands as $command) {
            if (in_array($command['name'], $this->allowCommands)) {
                Artisan::call($command['name']);
            } else {
                throw new BadException(__("extension::messages.command_register_invalid"));
            }
        }
    }
    private function publishAssets(ExtensionInstallPlan $plan): void
    {
        $source = base_path("extensions/{$plan->directory}/Resources/assets");
        $dest = public_path("extensions/{$plan->directory}");
        if ($plan->install) {
            if (!File::isDirectory($source)) {
                return;
            }
            if (!File::isDirectory($dest)) {
                File::makeDirectory($dest, 0755, true);
            }
            File::copyDirectory($source, $dest);
        } else {
            if (!File::isDirectory($dest)) {
                return;
            }
            File::deleteDirectory($dest);
        }
    }
    private function log(ExtensionInstallPlan $plan): void
    {
        logs()->info("install extension", $plan->toArray());
    }
}
