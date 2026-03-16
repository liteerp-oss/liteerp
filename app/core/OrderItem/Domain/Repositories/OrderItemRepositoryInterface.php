<?php

namespace Core\OrderItem\Domain\Repositories;

use Core\OrderItem\Domain\Entities\OrderItem;

interface OrderItemRepositoryInterface
{
    public function create(OrderItem $entity): OrderItem;
    public function show(array $data) : array;
    public function findById(array $data) : ?OrderItem;
    public function findByProductId(array $data) : ?OrderItem;
    public function index(array $data) : array;
    public function indexForStockMovementOut(array $data) : array;
    public function update(OrderItem $entity) : OrderItem;
    public function delete(OrderItem $entity) : OrderItem;
    public function summary(array $data) : ?array;
    public function cancelByOrderId(array $data) : bool;
}