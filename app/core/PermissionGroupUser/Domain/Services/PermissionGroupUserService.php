<?php

namespace Core\PermissionGroupUser\Domain\Services;

use App\Exceptions\BadException;
use Core\PermissionGroupUser\Domain\Entities\PermissionGroupUser;

interface PermissionGroupUserService
{
    public function create(array $data): PermissionGroupUser|BadException;
    public function show(array $data): PermissionGroupUser|BadException;
    public function delete(array $data): PermissionGroupUser|BadException;
}
