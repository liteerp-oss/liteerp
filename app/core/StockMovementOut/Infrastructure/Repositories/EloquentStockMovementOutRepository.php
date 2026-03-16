<?php

namespace Core\StockMovementOut\Infrastructure\Repositories;

use App\Models\StockMovementOutModel;
use Core\StockMovementOut\Domain\Repositories\StockMovementOutRepositoryInterface;
use Core\StockMovementOut\Domain\Entities\StockMovementOut;
use Illuminate\Support\Facades\DB;

class EloquentStockMovementOutRepository implements StockMovementOutRepositoryInterface
{
    public function create(StockMovementOut $entity): StockMovementOut
    {
        // TODO: Add actual database logic
        $create = StockMovementOutModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function findById(array $data): ?StockMovementOut
    {
        $row = StockMovementOutModel::select("stock_movements_out.*")
            ->join("order_items","order_items.id","=","stock_movements_out.order_item_id")
            ->join("orders","orders.id","=","order_items.order_id")
            ->where('stock_movements_out.id', $data['id'])
            ->where('orders.business_id', $data['business_id'])
            ->first()?->toArray();
        if (!$row) {
            return null;
        }
        return StockMovementOut::fromArray($row);
    }
    public function findExists(array $data): ?StockMovementOut
    {
        $row = StockMovementOutModel::select("stock_movements_out.*")
            ->join("order_items","order_items.id","=","stock_movements_out.order_item_id")
            ->join("orders","orders.id","=","order_items.order_id")
            ->where('stock_movements_out.order_item_id', $data['order_item_id'])
            ->where('orders.business_id', $data['business_id'])
            ->first()?->toArray();
        if (!$row) {
            return null;
        }
        return StockMovementOut::fromArray($row);
    }
    public function update(StockMovementOut $entity): StockMovementOut
    {
        StockMovementOutModel::where('id', $entity->id)
            ->update($entity->toArray());
        return $entity;
    }
}
