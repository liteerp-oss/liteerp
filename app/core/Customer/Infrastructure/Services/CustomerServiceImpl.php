<?php

namespace Core\Customer\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Customer\Domain\Services\CustomerService;
use Core\Customer\Domain\Repositories\CustomerRepositoryInterface;
use Core\Customer\Domain\Entities\Customer;

class CustomerServiceImpl implements CustomerService
{
    public function __construct(private CustomerRepositoryInterface $repo) {}

    public function create(array $data): Customer | BadException
    {
        $row = $this->repo->findByPhone($data);
        if ($row) {
            throw new BadException(__("customer::messages.phone_used"));
        }
        if(!empty($data['national_id'])) {
            $row = $this->repo->findByNationalId($data);
            if ($row) {
                throw new BadException(__("customer::messages.phone_used"));
            }    
        }
        
        $entity = Customer::fromArray($data);
        return $this->repo->create($entity);
    }
    public function update(array $data): Customer
    {
        $entity = $this->repo->findByPhone($data);
        $national = null;
        if(!empty($data['national_id'])) {
            $national = $this->repo->findByNationalId($data);
        }
        if ($entity && $entity->id !== $data['id']) {
            throw new BadException(__("customer::messages.phone_used"));
        } else if($national && $national->id !== $data['id']) {
            throw new BadException(__("customer::messages.national_used"));
        } else {
            $entity = $this->repo->findById($data);
            if (!$entity) {
                throw new BadException(__("customer::messages.not_found"));
            }
            $entity->name = $data['name'] ?? $entity->name;
            $entity->contact_name = $data['contact_name'] ?? $entity->contact_name;
            $entity->email = $data['email'] ?? $entity->email;
            $entity->phone = $data['phone'] ?? $entity->phone;
            $entity->address = $data['address'] ?? $entity->address;
            $entity->tax_code = $data['tax_code'] ?? $entity->tax_code;
            $entity->bank_name = $data['bank_name'] ?? $entity->bank_name;
            $entity->bank_account = $data['bank_account'] ?? $entity->bank_account;
            $entity->type = $data['type'] ?? $entity->type;
            $entity->group = $data['group'] ?? $entity->group;
            $entity->website = $data['website'] ?? $entity->website;
            $entity->note = $data['note'] ?? $entity->note;
            $entity->active = $data['active'] ?? $entity->active;
            $entity->national_id = $data['national_id'] ?? $entity->national_id;
            return $this->repo->update($entity);
        }
    }
    public function show(array $data): Customer|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("customer::messages.not_found"));
        }
        return $entity;
    }
    public function delete(array $data): Customer|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("customer::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
    public function createOrUpdate(array $data): Customer|BadException
    {
        $row = $this->repo->findByPhone($data);
        $entity = Customer::fromArray($data);
        if($row) {
            $row->address = $entity->address ?? $row->address;
            $row->name = $entity->name ?? $row->name;
            $row->email = $entity->email ?? $row->email;
            return $this->repo->update($row);
        }
        return $this->repo->create($entity);
    }
    public function getByNumberPhone(array $data): ?Customer
    {
        return $this->repo->findByPhone($data);
    }
}
