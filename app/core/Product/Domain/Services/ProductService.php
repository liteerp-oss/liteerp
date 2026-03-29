<?php

namespace Core\Product\Domain\Services;

use App\Exceptions\BadException;
use Core\Product\Domain\Entities\Product;

interface ProductService
{
    public function create(array $data): Product;
    public function show(array $data) : array | BadException;
    public function findById(array $data) : Product | BadException;
    public function update(array $data): Product;
    public function delete(array $data): Product;
}