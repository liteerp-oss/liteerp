<?php

namespace Core\Extension\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\Extension\Application\DTOs\CreateExtensionRequest;
use Core\Extension\Domain\Services\ExtensionService;
use Core\Extension\Domain\Supports\ExtensionChange;
use Core\Extension\Domain\Supports\ExtensionInstall;
use Core\Extension\Domain\Supports\ExtensionInstallExecutor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateExtension
{
    public function __construct(private ExtensionService $service, 
        private ExtensionInstall $install, private ExtensionInstallExecutor $exec) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateExtensionRequest::fromArray($data);
        $entity = $this->service->create($dto->toArray());
        Event::dispatch(Permission::EXTENSION_CREATE->value, [
            ...$dto->toArray(),
            ...$data,
            ...$entity->toArray(),
        ]);
        DB::commit();
        $this->exec->execute($this->install->installPlan($entity));   
        return $entity;
    }
}