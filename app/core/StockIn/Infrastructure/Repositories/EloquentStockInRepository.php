<?php

namespace Core\StockIn\Infrastructure\Repositories;

use App\Models\StockInModel;
use Core\StockIn\Domain\Repositories\StockInRepositoryInterface;
use Core\StockIn\Domain\Entities\StockIn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EloquentStockInRepository implements StockInRepositoryInterface
{
    public function create(StockIn $entity): StockIn
    {
        $create = StockInModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function findById(array $data): ?StockIn
    {
        $model = StockInModel::where('business_id', $data['business_id'])
            ->where('id', $data['id'])
            ->first()?->toArray();

        return $model ? StockIn::fromArray($model) : null;
    }
    public function findByInvoiceInId(array $data): ?StockIn
    {
        $model = StockInModel::where('business_id', $data['business_id'])
            ->where('invoice_in_id', $data['invoice_in_id'])
            ->first()?->toArray();
        if (!$model) {
            return null;
        }
        return StockIn::fromArray($model);
    }
    public function show(array $data): ?array
    {
        return StockInModel::select(
            "stock_ins.*",
            "suppliers.unit_name as unit_name",
            "suppliers.email as email",
            "suppliers.phone as phone",
            "suppliers.address as address",
            "purchases.id as purchase_id",
            "invoice_ins.document_no as document_no",
            "purchases.purchase_date as purchase_date",
            "invoice_ins.due_date as due_date",
            //"warehouses.name as warehouse_name",
            "purchase_user.name as purchase_approved_name",
            "stockin_user.name as approved_name",
            "purchases.note as purchase_note"
        )
            ->join("invoice_ins", "invoice_ins.id", "=", "stock_ins.invoice_in_id")
            ->join("purchases", "purchases.id", "=", "invoice_ins.purchase_id")
            ->leftJoin("users as purchase_user", "purchase_user.id", "=", "purchases.approved_by")
            ->leftJoin("users as stockin_user", "stockin_user.id", "=", "stock_ins.approved_by")
            ->join("purchase_items", "purchase_items.purchase_id", "=", "purchases.id")
            ->join("products", "products.id", "=", "purchase_items.product_id")
            //->join("warehouses","warehouses.id","=","products.warehouse_id")
            ->join("suppliers", "suppliers.id", "=", "purchases.supplier_id")
            ->where('stock_ins.business_id', $data['business_id'])
            ->where('stock_ins.id', $data['id'])
            ->first()?->toArray();
    }
    public function update(StockIn $entity): StockIn
    {
        StockInModel::where('id', $entity->id)
            ->update($entity->toArray());
        return $entity;
    }
}
