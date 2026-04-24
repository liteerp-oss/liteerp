<?php

namespace Core\StockOut\Infrastructure\Repositories;

use App\Models\StockOutModel;
use Core\StockOut\Domain\Repositories\StockOutRepositoryInterface;
use Core\StockOut\Domain\Entities\StockOut;
use Illuminate\Support\Facades\DB;

class EloquentStockOutRepository implements StockOutRepositoryInterface
{
    public function create(StockOut $entity): StockOut
    {
        $create = StockOutModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function findById(array $data): ?StockOut
    {
        $entity = StockOutModel::select("stock_outs.*")
        ->join("invoice_outs","invoice_outs.id","=","stock_outs.invoice_out_id")
        ->where('invoice_outs.order_id',$data['order_id'])
        ->where('stock_outs.id',$data['id'])
        ->where('stock_outs.business_id',$data['business_id'])->first()?->toArray();
        if(!$entity) {
            return null;
        }
        return StockOut::fromArray($entity);
    }
    public function getByInvoiceOutId(array $data): ?StockOut
    {
        $entity = StockOutModel::where('invoice_out_id',$data['invoice_out_id'])
        ->where('business_id',$data['business_id'])->first()?->toArray();
        if(!$entity) {
            return null;
        }
        return StockOut::fromArray($entity);
    }
    public function update(StockOut $entity) : StockOut {
        StockOutModel::where('id',$entity->id)
        ->where('business_id',$entity->business_id)
        ->update($entity->toArray());
        return $entity;
    }
    public function findByIdWithFullData(array $data): array
    {
        return StockOutModel::select("stock_outs.*",
        "orders.order_no as order_no",
        "orders.note as order_note",
        "orders.payment_method as payment_method",
        "orders.order_date as order_date",
        "orders.expected_delivery_date as expected_delivery_date",
        "orders.id as order_id",
        "orders.type",
        "shippings.receiver_name as receiver_name",
        "shippings.receiver_phone as receiver_phone",
        "shippings.receiver_address as receiver_address",
        "shippings.receiver_note as receiver_note",
        "shippings.shipping_unit as shipping_unit",
        "shippings.shipping_code as shipping_code",
        "shippings.shipping_fee_actual as shipping_fee_actual",
        "shippings.shipping_fee_estimated as shipping_fee_estimated",
        "shippings.shipped_at as shipped_at",
        "shippings.delivered_at as delivered_at",
        DB::raw("
        CASE
            WHEN shippings.shipping_fee_actual > 0
                THEN shippings.shipping_fee_actual
            ELSE shippings.shipping_fee_estimated
        END AS shipping_fee
        "),
        "shippings.id as shipping_id",
        "shippings.preferred_unit as preferred_unit",
        "shipping_providers.name as preferred_unit_name",
        "invoice_outs.document_no as invoice_no",
        "invoice_outs.payment_status as payment_status",
        "invoice_outs.subtotal as subtotal",
        "invoice_outs.tax as tax",
        "invoice_outs.discount as discount",
        "invoice_outs.total as total_adjusted",
        //"invoice_outs.total as total",
        "customers.name as customer_name",
        "customers.email as email",
        "customers.address as address",
        "customers.tax_code as tax_code",
        "customers.phone as phone",
        "staff.name as approved_name")
        ->join("invoice_outs","invoice_outs.id","=","stock_outs.invoice_out_id")
        ->join("orders","orders.id","=","invoice_outs.order_id")
        ->join("customers","customers.id","=","orders.customer_id")
        ->join('shippings','shippings.order_id','=','orders.id')
        ->join('shipping_providers','shipping_providers.id'
        ,'=','shippings.preferred_unit')
        ->leftJoin('users as staff','staff.id'
        ,'=','stock_outs.approved_by')
        ->where('stock_outs.id',$data['id'])
        ->where('orders.business_id',$data['business_id'])
        ->first()?->toArray();
    }
}