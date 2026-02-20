<?php

namespace Core\Permission\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Permission\Domain\Entities\Permission;
use Core\Permission\Domain\Repositories\PermissionRepositoryInterface;
use Core\Permission\Domain\Services\PermissionService;

class PermissionServiceImpl implements PermissionService
{
    public function __construct(private PermissionRepositoryInterface $repo) {}

    public function create(array $data): array|BadException
    {
        $permessions = [
            'group_id' => $data['group_id'],
            'permissions' => $data['permissions'],
        ];
        $arrayPermissions = [];
        foreach ($data['permissions'] as $key => $permission) {
            if (!in_array($permission, $arrayPermissions)) {
                $arrayPermissions[$key] = $permission;
                $permessions['permissions'][$key] = [
                    'group_id' => $data['group_id'],
                    'permission' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        return $this->repo->create($permessions);
    }

    public function show(array $data): array|BadException
    {
        $entity = $this->repo->show($data);
        return $entity;
    }
    public function findPermission(array $data): Permission|BadException
    {
        $entity = $this->repo->findByPermission($data);
        if (!$entity) {
            logs()->warning('Permission not found', ['permission' => $data]);
            throw new BadException(__('permission::messages.not_found', [
                'permission' => $data['permission']
            ]));
        }
        return $entity;
    }
    public function index(array $data): array|BadException
    {
        return $this->repo->index($data);
    }
}
