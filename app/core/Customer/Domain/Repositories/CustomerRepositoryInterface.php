<?php

namespace Core\Customer\Domain\Repositories;

use Core\Customer\Domain\Entities\Customer;

interface CustomerRepositoryInterface
{
    public function create(Customer $entity): Customer;
    public function update(Customer $entity): Customer;
    public function delete(Customer $entity): Customer;
    public function findById(array $data) : ?Customer;
    public function findByNationalId(array $data) : ?Customer;
    public function findByPhone(array $data) : ?Customer;
}