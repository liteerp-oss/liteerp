<?php

namespace Core\StockMovementIn\Infrastructure\Repositories;

use App\Models\StockMovementInModel;
use Core\StockMovementIn\Domain\Repositories\StockMovementInRepositoryInterface;
use Core\StockMovementIn\Domain\Entities\StockMovementIn;
use Illuminate\Support\Facades\DB;

class EloquentStockMovementInRepository implements StockMovementInRepositoryInterface
{
    public function create(StockMovementIn $entity): ?StockMovementIn
    {
        $create = StockMovementInModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function update(StockMovementIn $entity): ?StockMovementIn
    {
        StockMovementInModel::where('id',$entity->id)
        ->update($entity->toArray());
        return $entity;
    }
    public function findById(array $data): ?StockMovementIn
    {
        $row = StockMovementInModel::select("stock_movements_in.*")
        ->join("products","products.id","=","stock_movements_in.product_id")
        ->where('stock_movements_in.id',$data['id'])
        ->where('products.business_id',$data['business_id'])->first()?->toArray();
        if(!$row) {
            return $row;
        }
        return StockMovementIn::fromArray($data);
    }
    public function checkExists(array $data): ?StockMovementIn
    {
        $row = StockMovementInModel::select("stock_movements_in.*")
        ->join("products","products.id","=","stock_movements_in.product_id")
        ->where('stock_movements_in.stock_in_id',$data['stock_in_id'])
        ->where('stock_movements_in.product_id',$data['product_id'])
        ->where('stock_movements_in.warehouse_id',$data['warehouse_id'])
        ->where('products.business_id',$data['business_id'])->first()?->toArray();
        if(!$row) {
            return $row;
        }
        return StockMovementIn::fromArray($data);
    }
    public function index(array $data): array
    {
        $rows = StockMovementInModel::select("stock_movements_in.*")
        ->join("stock_ins","stock_ins.id"
            ,"=","stock_movements_in.stock_in_id")
        ->join("invoice_ins","invoice_ins.id"
            ,"=","stock_ins.invoice_in_id")
        ->where('invoice_ins.business_id',$data['business_id'])
        ->where('stock_movements_in.stock_in_id',$data['stock_in_id']);
        return $rows->paginate($data['limit'] ?? 300)->toArray();
    }
    public function getWithAvailabelQtyChange(array $data): ?array
    {
        return StockMovementInModel::select("stock_movements_in.*",
        DB::raw("stock_movements_in.qty_change - COALESCE(SUM(
                        order_items.buy_quantity 
                        + order_items.gift_quantity
                        + order_items.compensation_quantity
                        + order_items.conversion_quantity
                    ),0) as quantity"))
        ->leftJoin('order_items','order_items.stock_movements_in_id','=','stock_movements_in.id')
        ->where('stock_movements_in.id',$data['id'])
        ->groupBy("stock_movements_in.id")->first()?->toArray();
    }
}