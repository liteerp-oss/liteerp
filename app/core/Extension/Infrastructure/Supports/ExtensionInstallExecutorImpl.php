<?php

namespace Core\Extension\Infrastructure\Supports;

use App\Exceptions\BadException;
use Core\Extension\Application\DTOs\ExtensionInstallPlan;
use Core\Extension\Domain\Supports\ExtensionInstallExecutor;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ExtensionInstallExecutorImpl implements ExtensionInstallExecutor
{
    private $allowCommands = [
        "app:npmbuild"
    ];
    public function execute(ExtensionInstallPlan $plan): void
    {

        $this->runMigrations($plan);
        $this->runCommands($plan);
        $this->log($plan);
    }
    private function runMigrations(ExtensionInstallPlan $plan): void
    {
        foreach ($plan->migrations as $migration) {
            $filePath = "extensions/". $plan->directory ."/Database/Migrations/". $migration;
            if(file_exists(base_path($filePath))) {
               Artisan::call($plan->install ? "migrate" : "migrate:rollback",[
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
                Log::info('Run:'. $command['name']);
                Artisan::call($command['name']);
            } else {
                throw new BadException(__("extension::messages.command_register_invalid"));
            }
        }
    }
    private function log(ExtensionInstallPlan $plan): void
    {
        Log::info(json_encode($plan));
    }
}
