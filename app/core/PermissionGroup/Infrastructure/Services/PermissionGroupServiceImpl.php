<?php

namespace Core\PermissionGroup\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\PermissionGroup\Domain\Entities\PermissionGroup;
use Core\PermissionGroup\Domain\Repositories\PermissionGroupRepositoryInterface;
use Core\PermissionGroup\Domain\Services\PermissionGroupService;

class PermissionGroupServiceImpl implements PermissionGroupService
{
    public function __construct(private PermissionGroupRepositoryInterface $repo) {}

    public function create(array $data): PermissionGroup|BadException
    {
        $checkPermission = $this->repo->findByName($data);
        if ($checkPermission) {
            throw new BadException(__('permissiongroup::messages.permission_used'));
        }

        $entity = PermissionGroup::fromArray($data);
        $entity->setDefault();
        return $this->repo->create($entity);
    }
    public function createAdmin(array $data): PermissionGroup|BadException
    {
        $checkPermission = $this->repo->findByName($data);
        if ($checkPermission) {
            throw new BadException(__('permissiongroup::messages.permission_used'));
        }

        $entity = PermissionGroup::fromArray([
            ...$data
        ]);
        $entity->setAdmin();
        return $this->repo->create($entity);
    }

    public function show(array $data): PermissionGroup|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__('permissiongroup::messages.not_found'));
        }

        return $entity;
    }

    public function update(array $data): PermissionGroup|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__('permissiongroup::messages.not_found'));
        }

        $checkPermission = $this->repo->findByName($data);
        if ($checkPermission) {
            if($checkPermission->id !== $entity->id) {
                throw new BadException(__('permissiongroup::messages.permission_used'));
            }
        }
        $entity->name = $data['name'];
        return $this->repo->update($entity);
    }

    public function delete(array $data): PermissionGroup|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__('permissiongroup::messages.not_found'));
        }
        if($entity->isAdmin()) {
            throw new BadException(__('permissiongroup::messages.permission_admin_delete'));
        }
        return $this->repo->delete($entity);
    }
}
