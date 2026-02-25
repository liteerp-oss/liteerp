<?php

namespace Core\Permission\Domain\Services;

use App\Exceptions\BadException;
use Core\Permission\Domain\Entities\Permission;

interface PermissionService
{
    public function create(array $data): array|BadException;
    public function show(array $data): array|BadException;
    public function findPermission(array $data): Permission|BadException;
    public function getPermission(array $data): ?Permission;
    public function index(array $data): array|BadException;
    public function getUsersByPermission(array $data) : array;
}
