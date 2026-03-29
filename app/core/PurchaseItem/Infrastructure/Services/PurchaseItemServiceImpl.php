<?php

namespace Core\PurchaseItem\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\PurchaseItem\Domain\Services\PurchaseItemService;
use Core\PurchaseItem\Domain\Repositories\PurchaseItemRepositoryInterface;
use Core\PurchaseItem\Domain\Entities\PurchaseItem;

class PurchaseItemServiceImpl implements PurchaseItemService
{
    public function __construct(private PurchaseItemRepositoryInterface $repo) {}

    public function create(array $data): PurchaseItem | BadException
    {
        $entity = $this->repo->findByPurchaseIdAndProductId($data);
        if($entity) {
            throw new BadException(__("purchaseitem::messages.already_exists"));
        }
        $entity = PurchaseItem::fromArray($data);

        return $this->repo->create($entity);
    }
    public function update(array $data): PurchaseItem|BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("purchaseitem::messages.not_found"));
        }
        $entity->discount = $data['discount'] ?? $entity->discount;
        $entity->tax = $data['tax'] ?? $entity->tax;
        $entity->product_link = $data['product_link'] ?? $entity->product_link;
        $entity->buy_quantity = $data['buy_quantity'] ?? $entity->buy_quantity;
        $entity->gift_quantity = $data['gift_quantity'] ?? $entity->gift_quantity;
        $entity->compensation_quantity = $data['compensation_quantity'] ?? $entity->compensation_quantity;
        $entity->conversion_quantity = $data['conversion_quantity'] ?? $entity->conversion_quantity;
        $entity->unit_cost = $data['unit_cost'] ?? $entity->unit_cost;
        return $this->repo->update($entity);
    }
    public function findById(array $data): PurchaseItem|BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("purchaseitem::messages.not_found"));
    }
    public function show(array $data): array|BadException
    {
        return $this->repo->show($data) ?? throw new BadException(__("purchaseitem::messages.not_found"));
    }
    public function indexMinimal(array $data): array
    {
        return $this->repo->indexMinimal($data);
    }
    public function delete(array $data): PurchaseItem|BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("purchaseitem::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
}
