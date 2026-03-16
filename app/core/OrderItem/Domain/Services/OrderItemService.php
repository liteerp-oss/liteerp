<?php

namespace Core\OrderItem\Domain\Services;

use App\Exceptions\BadException;
use Core\OrderItem\Domain\Entities\OrderItem;

interface OrderItemService
{
    public function create(array $data): OrderItem;
    public function index(array $data): array;
    public function show(array $data) : array | BadException;
    public function update(array $data) : OrderItem;
    public function delete(array $data) : OrderItem | BadException;
    public function findById(array $data) : OrderItem | BadException;
    public function summary(array $data): ?array;
    public function indexForStockMovementOut(array $data) : array;
    public function cancelByOrderItem(array $data) : bool;
}