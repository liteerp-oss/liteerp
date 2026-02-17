<?php

namespace Core\Warehouse\Domain\Services;

use App\Exceptions\BadException;
use Core\Warehouse\Domain\Entities\Warehouse;

interface WarehouseService
{
    public function create(array $data): Warehouse | BadException;
    public function show(array $data) : Warehouse;
    public function update(array $data): Warehouse | BadException;
    public function delete(array $data): Warehouse | BadException;
}