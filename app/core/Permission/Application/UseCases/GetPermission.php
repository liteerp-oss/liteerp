<?php

namespace Core\Permission\Application\UseCases;
use Core\Permission\Application\DTOs\GetPermissionRequest;
use Core\Permission\Domain\Entities\Permission;
use Core\Permission\Domain\Services\PermissionService;

class GetPermission
{
    public function __construct(
        private PermissionService $service
    ) {}

    public function handle(array $data): ?Permission
    {
        $dto = GetPermissionRequest::fromArray($data);
        return $this->service->getPermission($dto->toArray());
    }
}
