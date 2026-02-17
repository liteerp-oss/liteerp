<?php

namespace Core\Warehouse\Domain\Repositories;

use Core\Warehouse\Domain\Entities\Warehouse;

interface WarehouseRepositoryInterface
{
    public function create(Warehouse $entity): Warehouse;
    public function checkNameExists(Warehouse $entity) : bool;
    public function findById(array $data) : ?Warehouse;
    public function update(Warehouse $entity): Warehouse;
    public function delete(Warehouse $entity): Warehouse;
}