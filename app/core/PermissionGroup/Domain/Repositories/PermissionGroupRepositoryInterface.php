<?php

namespace Core\PermissionGroup\Domain\Repositories;

use Core\PermissionGroup\Domain\Entities\PermissionGroup;

interface PermissionGroupRepositoryInterface
{
    public function create(PermissionGroup $entity): PermissionGroup;
    public function findById(array $data): ?PermissionGroup;
    public function findByName(array $data): ?PermissionGroup;
    public function update(PermissionGroup $entity): PermissionGroup;
    public function delete(PermissionGroup $entity): PermissionGroup;
}
