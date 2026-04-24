<?php

namespace Core\Customer\Domain\Services;

use App\Exceptions\BadException;
use Core\Customer\Domain\Entities\Customer;

interface CustomerService
{
    public function create(array $data): Customer | BadException;
    public function createOrUpdate(array $data): Customer | BadException;
    public function update(array $data): Customer | BadException;
    public function delete(array $data): Customer | BadException;
    public function show(array $data) : Customer | BadException;
    public function getByNumberPhone(array $data) : ?Customer;
}