<?php

namespace Core\StockMovementIn\Domain\Services;

use App\Exceptions\BadException;
use Core\StockMovementIn\Domain\Entities\StockMovementIn;

interface StockMovementInService
{
    public function create(array $data): StockMovementIn | BadException;
    public function update(array $data): StockMovementIn | BadException;
    public function index(array $data): array;
}