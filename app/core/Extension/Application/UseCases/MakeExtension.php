<?php

namespace Core\Extension\Application\UseCases;

use Core\Extension\Application\DTOs\MakeExtensionCommand;
use Core\Extension\Domain\Services\ExtensionService;
use Core\Extension\Domain\Supports\ExtensionInstall;
use Core\Extension\Domain\Supports\ExtensionInstallExecutor;
use Illuminate\Support\Facades\DB;

class MakeExtension
{
    public function __construct(private ExtensionService $service,
        private ExtensionInstall $install, private ExtensionInstallExecutor $exec) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = MakeExtensionCommand::fromArray($data);
        $entity = $this->service->make($dto->toArray());
        DB::commit(); 
        $this->exec->execute($this->install->installPlan($entity));  
        return $entity;
    }
}