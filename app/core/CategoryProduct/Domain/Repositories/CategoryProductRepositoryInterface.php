<?php

namespace Core\CategoryProduct\Domain\Repositories;

use App\Exceptions\BadException;
use Core\CategoryProduct\Domain\Entities\CategoryProduct;

interface CategoryProductRepositoryInterface
{
    public function create(CategoryProduct $entity): CategoryProduct;
    public function checkNameExists(array $data): ?CategoryProduct;
    public function getByName(array $data):CategoryProduct;
    public function findById(array $data) : ?CategoryProduct;
    public function update(CategoryProduct $entity) : CategoryProduct;
    public function delete(CategoryProduct $entity) : CategoryProduct;
}