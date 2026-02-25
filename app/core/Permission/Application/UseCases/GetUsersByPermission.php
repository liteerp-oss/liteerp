<?php

namespace Core\Permission\Application\UseCases;
use Core\Permission\Application\DTOs\GetUsersByPermissionRequest;
use Core\Permission\Domain\Services\PermissionService;

class GetUsersByPermission
{
    public function __construct(
        private PermissionService $service
    ) {}

    public function handle(array $data): array
    {
        $dto = GetUsersByPermissionRequest::fromArray($data);
        return $this->service->getUsersByPermission($dto->toArray());
    }
}
