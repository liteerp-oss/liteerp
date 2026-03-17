<?php

namespace Core\StockMovementIn\Domain\Repositories;

use App\Exceptions\BadException;
use Core\StockMovementIn\Domain\Entities\StockMovementIn;

interface StockMovementInRepositoryInterface
{
    public function create(StockMovementIn $entity): ?StockMovementIn;
    public function update(StockMovementIn $data): ?StockMovementIn;
    public function findById(array $data) : ?StockMovementIn;
    public function checkExists(array $data) : ?StockMovementIn;
    public function getWithAvailabelQtyChange(array $data): ?array;
    public function index(array $data) : array;
}