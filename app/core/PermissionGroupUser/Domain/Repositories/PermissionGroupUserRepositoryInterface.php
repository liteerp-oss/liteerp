<?php

namespace Core\PermissionGroupUser\Domain\Repositories;

use Core\PermissionGroupUser\Domain\Entities\PermissionGroupUser;

interface PermissionGroupUserRepositoryInterface
{
    public function create(PermissionGroupUser $entity): PermissionGroupUser;
    public function findById(array $data): ?PermissionGroupUser;
    public function findByGroupAndUser(array $data): ?PermissionGroupUser;
    public function delete(PermissionGroupUser $entity): PermissionGroupUser;
}
