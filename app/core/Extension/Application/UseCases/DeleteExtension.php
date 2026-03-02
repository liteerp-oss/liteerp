<?php

namespace Core\Extension\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\Extension\Application\DTOs\DeleteExtensionRequest;
use Core\Extension\Domain\Services\ExtensionService;
use Core\Extension\Domain\Supports\ExtensionInstall;
use Core\Extension\Domain\Supports\ExtensionInstallExecutor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteExtension
{
    public function __construct(private ExtensionService $service,
        private ExtensionInstall $install, private ExtensionInstallExecutor $exec) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteExtensionRequest::fromArray($data);
        $findById = $this->service->findById($dto->toArray());
        Event::dispatch(Permission::EXTENSION_DELETE->value, [
            ...$dto->toArray(),
            ...$data,
            ...$findById->toArray(),
        ]);
        DB::commit();
        $this->exec->execute($this->install->uninstallPlan($findById));   
        $entity = $this->service->delete($findById->toArray());
        return $entity;
    }
}