<?php

namespace Core\OrderItem\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\OrderItem\Domain\Services\OrderItemService;
use Core\OrderItem\Domain\Repositories\OrderItemRepositoryInterface;
use Core\OrderItem\Domain\Entities\OrderItem;

class OrderItemServiceImpl implements OrderItemService
{
    public function __construct(private OrderItemRepositoryInterface $repo) {}

    public function create(array $data): OrderItem
    {
        $entity = OrderItem::fromArray($data);
        if($this->repo->findByProductId($data)) {
            throw new BadException(__("orderitem::messages.product_used"));
        }
        return $this->repo->create($entity);
    }
    public function index(array $data): array
    {
        if(!empty($data['summary'])) {
            return $this->repo->summary($data) ?? [
                'total_quantity' => floatval(0),
                'subtotal' => floatval(0),
                'total_discount' => floatval(0),
                'total_tax' => floatval(0),
                'total' => floatval(0),
                'shipping_fee' => floatval(0)
            ];
        }
        return $this->repo->index($data);
    }
    public function show(array $data): array | BadException
    {
        $entity = $this->repo->show($data);
        if(!$entity) {
            throw new BadException(__("orderitem::messages.not_found"));
        }
        return $entity;
    }
    public function update(array $data): OrderItem
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("orderitem::messages.not_found"));
        }
        $entity->discount = $data['discount'];
        $entity->buy_quantity = $data['buy_quantity'];
        $entity->gift_quantity = $data['gift_quantity'];
        $entity->compensation_quantity = $data['compensation_quantity'];
        $entity->conversion_quantity = $data['conversion_quantity'];
        $entity->tax = $data['tax'];
        $entity->price = $data['price'];
        return $this->repo->update($entity);
    }
    public function delete(array $data): OrderItem | BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("orderitem::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
    public function findById(array $data): OrderItem|BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("orderitem::messages.not_found"));
        }
        return $entity;
    }
    public function summary(array $data): ?array {
        return $this->repo->summary($data);
    }
    public function indexForStockMovementOut(array $data) : array {
        return $this->repo->indexForStockMovementOut($data);
    }
    public function cancelByOrderItem(array $data): bool
    {
        return $this->repo->cancelByOrderId($data);
    }
}
