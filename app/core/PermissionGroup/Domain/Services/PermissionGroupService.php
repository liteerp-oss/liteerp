<?php

namespace Core\PermissionGroup\Domain\Services;

use App\Exceptions\BadException;
use Core\PermissionGroup\Domain\Entities\PermissionGroup;

interface PermissionGroupService
{
    public function create(array $data): PermissionGroup|BadException;
    public function createAdmin(array $data): PermissionGroup|BadException;
    public function show(array $data): PermissionGroup|BadException;
    public function update(array $data): PermissionGroup|BadException;
    public function delete(array $data): PermissionGroup|BadException;
}
