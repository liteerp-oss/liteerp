<?php

namespace Core\Permission\Infrastructure\Repositories;

use App\Models\PermissionModel;
use Core\Permission\Domain\Entities\Permission;
use Core\Permission\Domain\Repositories\PermissionRepositoryInterface;

class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    public function create(array $data): array
    {
        PermissionModel::where('group_id', $data['group_id'])->delete();
        PermissionModel::insert($data['permissions']);
        return $data;
    }

    public function show(array $data): array
    {
        $row = PermissionModel::select('permissions.*')
            ->join('permission_groups', 'permission_groups.id', '=', 'permissions.group_id')
            ->where('permission_groups.business_id', $data['business_id'])
            ->where('permissions.group_id', $data['group_id'])
            ->pluck('permissions.permission')->toArray();
        return $row;
    }

    public function findByPermission(array $data): ?Permission
    {
        $row = PermissionModel::select('permissions.*')
            ->join('permission_groups', 'permission_groups.id', '=', 'permissions.group_id')
            ->join('permission_group_user', 'permission_group_user.group_id', '=', 'permission_groups.id')
            ->where('permission_groups.business_id', $data['business_id'])
            ->where('permission_group_user.account_id', $data['user_id'])
            ->where('permissions.permission', $data['permission'])
            ->first()?->toArray();
        if (!$row) {
            return null;
        }

        return Permission::fromArray($row);
    }
    public function index(array $data): array
    {
        $list = PermissionModel::select('permissions.*')
            ->join('permission_groups', 'permission_groups.id', '=', 'permissions.group_id')
            ->join('permission_group_user', 'permission_group_user.group_id', '=', 'permission_groups.id')
            ->where('permission_groups.business_id', $data['business_id'])
            ->where('permission_group_user.account_id', $data['user_id'])
            //->where('permission_groups.user_id', $data['user_id'])
            ->pluck('permissions.permission')
            ->toArray();
        return $list;
    }
    public function getUsersByPermission(array $data): array
    {
        $users = PermissionModel::select('users.name as user_name','users.email',
                'users.lang','users.id as user_id')
            ->join('permission_groups', 'permission_groups.id', '=', 'permissions.group_id')
            ->join('permission_group_user', 'permission_group_user.group_id', '=', 'permission_groups.id')
            ->join('users', 'users.id', '=', 'permission_group_user.account_id')
            ->where('permission_groups.business_id', $data['business_id'])
            ->where('permission_group_user.account_id','!=', $data['user_id']);

            if(!empty($data['permissions'])) {
                $users = $users->whereIn('permissions.permission',$data['permissions']);
            }

            return $users->groupBy("users.id")->get()->toArray();
    }
}
