<?php

namespace Core\Permission\Domain\Repositories;

use Core\Permission\Domain\Entities\Permission;

interface PermissionRepositoryInterface
{
    public function create(array $entity): array;
    public function show(array $data): ?array;
    public function findByPermission(array $data): ?Permission;
    public function index(array $data): array;
    public function getUsersByPermission(array $data) : array;
}
