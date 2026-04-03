<?php

namespace Core\Customer\Infrastructure\Repositories;

use App\Models\CustomerModel;
use Core\Customer\Domain\Repositories\CustomerRepositoryInterface;
use Core\Customer\Domain\Entities\Customer;
use Illuminate\Support\Facades\DB;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(array $data): ?Customer
    {
        $row = CustomerModel::where('id',$data['id'])
        ->where('business_id',$data['business_id'])->first()?->toArray();
        if(!$row) {
            return null;
        }
        $entity = Customer::fromArray($row);
        return $entity;
    }
    public function findByPhone(array $data): ?Customer
    {
        $row = CustomerModel::where('business_id',$data['business_id'])
        ->where('phone',$data['phone'])
        ->first()?->toArray();
        if(!$row) {
            return null;
        }
        $entity = Customer::fromArray($row);
        return $entity;
    }
    public function findByNationalId(array $data): ?Customer
    {
        $row = CustomerModel::where('business_id',$data['business_id'])
        ->where('national_id',$data['national_id'])
        ->first()?->toArray();
        if(!$row) {
            return null;
        }
        $entity = Customer::fromArray($row);
        return $entity;
    }
    public function create(Customer $entity): Customer
    {
        // TODO: Add actual database logic
        $create = CustomerModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function update(Customer $entity) : Customer {
        CustomerModel::where('id',$entity->id)
        ->where('business_id',$entity->business_id)
        ->update($entity->toArray());
        return $entity;
    }
    public function delete(Customer $entity): Customer
    {
        CustomerModel::where('id',$entity->id)
        ->delete();
        return $entity;
    }
}