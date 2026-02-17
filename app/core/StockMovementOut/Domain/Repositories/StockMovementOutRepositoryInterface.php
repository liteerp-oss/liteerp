<?php

namespace Core\StockMovementOut\Domain\Repositories;

use Core\StockMovementOut\Domain\Entities\StockMovementOut;

interface StockMovementOutRepositoryInterface
{
    public function create(StockMovementOut $entity): StockMovementOut;
    public function update(StockMovementOut $entity): StockMovementOut;
    public function findById(array $data): ?StockMovementOut;
    public function findExists(array $data): ?StockMovementOut;
}