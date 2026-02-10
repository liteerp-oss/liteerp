<?php

namespace Core\Inventory\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Inventory\Domain\Services\InventoryService;
use Core\Inventory\Domain\Repositories\InventoryRepositoryInterface;
use Core\Inventory\Domain\Entities\Inventory;
use Illuminate\Support\Facades\Log;

class InventoryServiceImpl implements InventoryService
{
    public function __construct(private InventoryRepositoryInterface $repo) {}

    public function create(array $data): Inventory
    {
        $entity = $this->repo->findByOneByProductAndWarehouse($data);
        if($entity) {
            throw new BadException(__("inventory::messages.already_exists"));
        }
        $entity = Inventory::fromArray($data);
        $entity->isCreate();
        return $this->repo->create(Inventory::fromArray($data));
    }
    public function show(array $data): Inventory|BadException
    {
        return $this->repo->findByOneByProductAndWarehouse($data) ?? throw new BadException(__("inventory::messages.not_found"));
    }
    public function update(array $data): Inventory|BadException
    {
        $entity = $this->repo->findByOneByProductAndWarehouse($data);
        if(!$entity) {
            throw new BadException(__("inventory::messages.not_exists"));
        }
        $entity->quantityCalculator(intval($data['quantity']));
        $entity->reservedQuantityCalculator(intval($data['reserved_qty']));
        if($entity->quantity < $entity->reserved_qty) {
            throw new BadException(__("inventory::messages.not_enough"));
        }
        return $this->repo->update($entity);
    }
    public function updateById(array $data): Inventory|BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("inventory::messages.not_exists"));
        }
        $entity->quantityCalculator(intval($data['quantity']));
        $entity->reservedQuantityCalculator(intval($data['reserved_qty']));
        if($entity->quantity < $entity->reserved_qty) {
            throw new BadException(__("inventory::messages.not_enough"));
        }
        return $this->repo->update($entity);
    }
    public function index(array $data): array
    {
        if(!empty($data['customer_group_id'])) {
            return $this->repo->indexForOrder($data);
        }
        return $this->repo->index($data);
    }
    public function findById(array $data): Inventory|BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("inventory::messages.not_found"));
    }
    public function getById(array $data): ?Inventory
    {
        return $this->repo->findById($data);
    }
    public function getByOneByProductAndWarehouse(array $data): ?Inventory
    {
        return $this->repo->findByOneByProductAndWarehouse($data);
    }
}
