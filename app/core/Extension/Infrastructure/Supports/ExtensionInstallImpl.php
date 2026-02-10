<?php

namespace Core\Extension\Infrastructure\Supports;

use Core\Extension\Application\DTOs\ExtensionInstallPlan;
use Core\Extension\Domain\Entities\Extension;
use Core\Extension\Domain\Supports\ExtensionInstall;

class ExtensionInstallImpl implements ExtensionInstall
{
    public function installPlan(Extension $extension): ExtensionInstallPlan
    {
        $install = base_path("extensions/" . $extension->directory . "/Install.php");
            if (!file_exists($install)) {
                throw new \Exception(__("extension::messages.install_file_not_found"));
        }
        $config =  require_once $install;
        return new ExtensionInstallPlan(
            commands: $config['install']["commands"],
            migrations: $config['install']["migrations"],
            warnings: [],
            directory: $extension->directory,
            install: true
        );
    }
    public function uninstallPlan(Extension $extension): ExtensionInstallPlan
    {
        $install = base_path("extensions/" . $extension->directory . "/Install.php");
            if (!file_exists($install)) {
                throw new \Exception(__("extension::messages.install_file_not_found"));
        }
        $config =  require_once $install;
        return new ExtensionInstallPlan(
            commands: $config['uninstall']["commands"],
            migrations: $config['uninstall']["migrations"],
            warnings: [],
            directory: $extension->directory,
            install: false
        );
    }
}
