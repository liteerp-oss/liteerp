<?php

namespace Core\PurchaseItem\Infrastructure\Repositories;

use App\Models\PurchaseItemModel;
use App\Supports\Hooks\HookDispatcher;
use Core\PurchaseItem\Domain\Repositories\PurchaseItemRepositoryInterface;
use Core\PurchaseItem\Domain\Entities\PurchaseItem;
use Illuminate\Support\Facades\DB;

class EloquentPurchaseItemRepository implements PurchaseItemRepositoryInterface
{
    public function __construct() {}

    public function create(PurchaseItem $entity): PurchaseItem
    {
        $create = PurchaseItemModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function findByPurchaseIdAndProductId(array $data): ?PurchaseItem
    {
        $row = PurchaseItemModel::select("purchase_items.*")
        ->join("purchases","purchases.id","=","purchase_items.purchase_id")
        ->where('purchase_items.purchase_id',$data['purchase_id'])
        ->where('purchase_items.product_id',$data['product_id'])->first()?->toArray();
        if(!$row) {
            return $row;
        }
        return PurchaseItem::fromArray($row);
    }
    public function findById(array $data): PurchaseItem
    {
        $row = PurchaseItemModel::select("purchase_items.*")
        ->join("purchases","purchases.id","=","purchase_items.purchase_id")
        ->where('purchases.business_id',$data['business_id'])
        ->where('purchase_items.id',$data['id'])
        ->first()?->toArray();
        if(!$row) {
            return $row;
        }
        return PurchaseItem::fromArray($row);
    }
    public function show(array $data): array
    {
        $row = PurchaseItemModel::select("purchase_items.*","products.name","products.sku")
        ->join("purchases","purchases.id","=","purchase_items.purchase_id")
        ->join("products","products.id","=","purchase_items.product_id")
        ->where('purchases.business_id',$data['business_id'])
        ->where('purchase_items.id',$data['id'])
        ->first()?->toArray();
        if(!$row) {
            return $row;
        }
        return $row;
    }
    public function update(PurchaseItem $entity): PurchaseItem
    {
        PurchaseItemModel::where('id',$entity->id)
        ->update($entity->toArray());
        return $entity;
    }
    public function delete(PurchaseItem $entity): PurchaseItem
    {
        PurchaseItemModel::where('id',$entity->id)
        ->delete();
        return $entity;
    }
    public function indexMinimal(array $data): array
    {
        return PurchaseItemModel::select("purchase_items.*")
        ->join("purchases","purchases.id","=","purchase_items.purchase_id")
        ->where('purchase_items.purchase_id', $data['purchase_id'])
        ->where('purchases.business_id', $data['business_id'])
        ->limit(300)
        ->get()->toArray();
    }
}
