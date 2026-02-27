<?php

namespace Core\Business\Domain\Repositories;

use Core\Business\Domain\Entities\Business;

interface BusinessRepositoryInterface
{
    public function create(Business $entity): Business;
    public function checkExists(Business $entity): bool;
    public function findById(array $data): ?Business;
    public function findByName(array $data): ?Business;
    public function findByIdWithFullData(array $data): ?array;
    public function update(Business $entity): Business;
    public function all() : array;
}