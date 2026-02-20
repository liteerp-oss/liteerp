<?php

namespace Core\PermissionGroupUser\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\PermissionGroupUser\Domain\Entities\PermissionGroupUser;
use Core\PermissionGroupUser\Domain\Repositories\PermissionGroupUserRepositoryInterface;
use Core\PermissionGroupUser\Domain\Services\PermissionGroupUserService;

class PermissionGroupUserServiceImpl implements PermissionGroupUserService
{
    public function __construct(private PermissionGroupUserRepositoryInterface $repo) {}

    public function create(array $data): PermissionGroupUser|BadException
    {
        $checkExists = $this->repo->findByGroupAndUser($data);
        if ($checkExists) {
            throw new BadException(__('permissiongroupuser::messages.assigned'));
        }

        $entity = PermissionGroupUser::fromArray($data);
        return $this->repo->create($entity);
    }

    public function show(array $data): PermissionGroupUser|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__('permissiongroupuser::messages.not_found'));
        }

        return $entity;
    }

    public function delete(array $data): PermissionGroupUser|BadException
    {
        $entity = $this->repo->findByGroupAndUser($data);
        if (!$entity) {
            throw new BadException(__('permissiongroupuser::messages.not_found'));
        }

        return $this->repo->delete($entity);
    }
}
