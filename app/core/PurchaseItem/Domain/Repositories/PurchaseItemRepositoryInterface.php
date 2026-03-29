<?php

namespace Core\PurchaseItem\Domain\Repositories;

use Core\PurchaseItem\Domain\Entities\PurchaseItem;

interface PurchaseItemRepositoryInterface
{
    public function create(PurchaseItem $entity): PurchaseItem;
    public function findByPurchaseIdAndProductId(array $data): ?PurchaseItem;
    public function findById(array $data): ?PurchaseItem;
    public function show(array $data): ?array;
    public function update(PurchaseItem $entity): PurchaseItem;
    public function delete(PurchaseItem $entity): PurchaseItem;
    public function indexMinimal(array $data) : array;
}