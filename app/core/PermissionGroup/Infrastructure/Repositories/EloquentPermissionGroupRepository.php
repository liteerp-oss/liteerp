<?php

namespace Core\PermissionGroup\Infrastructure\Repositories;

use App\Models\PermissionGroupModel;
use Core\PermissionGroup\Domain\Entities\PermissionGroup;
use Core\PermissionGroup\Domain\Repositories\PermissionGroupRepositoryInterface;

class EloquentPermissionGroupRepository implements PermissionGroupRepositoryInterface
{
    public function create(PermissionGroup $entity): PermissionGroup
    {
        $create = PermissionGroupModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }

    public function findById(array $data): ?PermissionGroup
    {
        $row = PermissionGroupModel::where('id', $data['id'])->first()?->toArray();
        if (!$row) {
            return null;
        }
        return PermissionGroup::fromArray($row);
    }

    public function findByName(array $data): ?PermissionGroup
    {
        $row = PermissionGroupModel::where('name', $data['name'])
        ->where('business_id',$data['business_id'])->first()?->toArray();
        if (!$row) {
            return null;
        }
        return PermissionGroup::fromArray($row); 
    }

    public function update(PermissionGroup $entity): PermissionGroup
    {
        PermissionGroupModel::where('id', $entity->id)->update([
            'name' => $entity->name,
        ]);

        return $entity;
    }

    public function delete(PermissionGroup $entity): PermissionGroup
    {
        PermissionGroupModel::where('id', $entity->id)->delete();
        return $entity;
    }
}
