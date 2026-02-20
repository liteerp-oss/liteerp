<?php

namespace Core\Permission\Application\UseCases;
use Core\Permission\Application\DTOs\CheckPermissionRequest;
use Core\Permission\Domain\Services\PermissionService;

class CheckPermission
{
    public function __construct(
        private PermissionService $service
    ) {}

    public function handle(array $data)
    {
        $dto = CheckPermissionRequest::fromArray($data);
        $this->service->findPermission($dto->toArray());
        return;
    }
}
