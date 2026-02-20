<?php

namespace Core\PermissionGroupUser\Infrastructure\Repositories;

use App\Models\PermissionGroupUserModel;
use Core\PermissionGroupUser\Domain\Entities\PermissionGroupUser;
use Core\PermissionGroupUser\Domain\Repositories\PermissionGroupUserRepositoryInterface;

class EloquentPermissionGroupUserRepository implements PermissionGroupUserRepositoryInterface
{
    public function create(PermissionGroupUser $entity): PermissionGroupUser
    {
        $id = PermissionGroupUserModel::create([
            'group_id' => $entity->group_id,
            'account_id' => $entity->account_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $entity->id = $id['id'];
        return $entity;
    }

    public function findById(array $data): ?PermissionGroupUser
    {
        $row = PermissionGroupUserModel::select("permission_group_user.*")
            ->join("permission_groups","permission_groups.id","=","permission_group_user.group_id")
            ->where('permission_group_user.id', $data['id'])
            ->where('permission_groups.business_id', $data['business_id'])
            ->where('permission_group_user.account_id', $data['account_id'])
            ->first();

        if (!$row) {
            return null;
        }

        return PermissionGroupUser::fromArray((array) $row);
    }

    public function findByGroupAndUser(array $data): ?PermissionGroupUser
    {
        $row = PermissionGroupUserModel::select("permission_group_user.*")
            ->join("permission_groups","permission_groups.id","=","permission_group_user.group_id")
            ->where('permission_group_user.group_id', $data['group_id'])
            ->where('permission_group_user.account_id', $data['account_id'])
            ->where('permission_groups.business_id', $data['business_id'])
            ->first()?->toArray();

        if (!$row) {
            return null;
        }

        return PermissionGroupUser::fromArray($row);
    }

    public function delete(PermissionGroupUser $entity): PermissionGroupUser
    {
        PermissionGroupUserModel::where('id', $entity->id)
            ->delete();

        return $entity;
    }
}
