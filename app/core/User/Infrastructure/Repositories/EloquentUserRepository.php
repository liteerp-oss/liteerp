<?php

namespace Core\User\Infrastructure\Repositories;

use App\Models\User as ModelsUser;
use Core\User\Domain\Repositories\UserRepositoryInterface;
use Core\User\Domain\Entities\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(array $data): ?User
    {
        $row = ModelsUser::select("users.*","permission_groups.name as group")
        ->join("permission_group_user","permission_group_user.account_id","=","users.id")
        ->join("permission_groups","permission_groups.id","=","permission_group_user.group_id")
        ->where("permission_groups.business_id",$data['business_id'])
        ->where('users.id',$data['id'])
        ->first()?->toArray();
        if(!$row) {
            return null;
        }
        return User::fromArray($row);
    }
    public function findByEmailOnSystem(array $data): ?User{
        $row = ModelsUser::where('users.email',$data['email'])
        ->first()?->toArray();
        if(!$row) {
            return null;
        }
        return User::fromArray($row);
    }
    public function findByEmail(array $data): ?User
    {
        $row = ModelsUser::select("users.*","permission_groups.name as group")
        ->join("permission_group_user","permission_group_user.account_id","=","users.id")
        ->join("permission_groups","permission_groups.id","=","permission_group_user.group_id")
        ->where("permission_groups.business_id",$data['business_id'])
        ->where('users.email',$data['email'])
        ->first()?->toArray();
        if(!$row) {
            return null;
        }
        return User::fromArray($row);
    }
    public function getAll() : array {
        return ModelsUser::select("users.*",
            "permission_groups.name as group",
            "permission_groups.business_id",
            "business.name as business_name",
            "business.address as business_address")
        ->join("permission_group_user","permission_group_user.account_id","=","users.id")
        ->join("permission_groups","permission_groups.id","=","permission_group_user.group_id")
        ->join("business","business.id","=","permission_groups.business_id")
        ->get()->toArray();
    }
}