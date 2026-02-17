<?php

namespace Core\Warehouse\Infrastructure\Repositories;

use App\Models\WarehouseModel;
use Core\Warehouse\Domain\Repositories\WarehouseRepositoryInterface;
use Core\Warehouse\Domain\Entities\Warehouse;

class EloquentWarehouseRepository implements WarehouseRepositoryInterface
{
    public function create(Warehouse $entity): Warehouse
    {
        $entity->setActive();
        $create = WarehouseModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function checkNameExists(Warehouse $entity): bool
    {
        $exists = WarehouseModel::where('name',$entity->name)
        ->where('business_id',$entity->business_id);
        return $exists->count() == false ? false : true;
    }
    public function findById(array $data): ?Warehouse
    {
        $exists = WarehouseModel::where('id',$data['id'])
        ->where('business_id',$data['business_id']);
        if($exists->count() == false ) {
            return null;
        }
        $exists = $exists->first();
        $entity = Warehouse::fromArray($exists->toArray());
        return $entity;
    }
    public function update(Warehouse $entity): Warehouse
    {
        WarehouseModel::where('id',$entity->id)
        ->where('business_id',$entity->business_id)
        ->update($entity->toArray());
        return $entity;
    }
    public function delete(Warehouse $entity): Warehouse
    {
        WarehouseModel::where('id',$entity->id)
        ->where('business_id',$entity->business_id)
        ->delete();
        return $entity;
    }
}