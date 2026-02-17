<?php

namespace Core\StockMovementOut\Domain\Services;

use Core\StockMovementOut\Domain\Entities\StockMovementOut;

interface StockMovementOutService
{
    public function create(array $data): StockMovementOut;
    public function update(array $data): StockMovementOut;
    public function findById(array $data) : StockMovementOut;
}