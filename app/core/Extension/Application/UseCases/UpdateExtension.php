<?php

namespace Core\Extension\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\Extension\Application\DTOs\UpdateExtensionRequest;
use Core\Extension\Domain\Services\ExtensionService;
use Core\Extension\Domain\Supports\ExtensionChange;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateExtension
{
    public function __construct(private ExtensionService $service) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = UpdateExtensionRequest::fromArray($data);
        $update = $this->service->update($dto->toArray());
        Event::dispatch(Permission::EXTENSION_UPDATE->value, [
            ...$dto->toArray(),
            ...$data,
            ...$update->toArray(),
        ]);
        DB::commit();
        return $update;
    }
}