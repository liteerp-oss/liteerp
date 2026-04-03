<?php

namespace Core\InvoiceOut\Infrastructure\Repositories;

use App\Models\InvoiceOutModel;
use Core\InvoiceOut\Domain\Repositories\InvoiceOutRepositoryInterface;
use Core\InvoiceOut\Domain\Entities\InvoiceOut;
use Illuminate\Support\Facades\DB;

class EloquentInvoiceOutRepository implements InvoiceOutRepositoryInterface
{
    public function create(InvoiceOut $entity): InvoiceOut
    {
        // TODO: Add actual database logic
        $create = InvoiceOutModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function findById(array $data): ?InvoiceOut
    {
        $row = InvoiceOutModel::where('id',$data['id'])
        ->where('business_id',$data['business_id'])->first()?->toArray();
        return $row ? InvoiceOut::fromArray($row) : null;
    }
    public function findByOrderId(array $data): ?InvoiceOut
    {
        $row = InvoiceOutModel::where('order_id',$data['order_id'])
        ->where('business_id',$data['business_id'])->first()?->toArray();
        return $row ? InvoiceOut::fromArray($row) : null;
    }
    public function index(array $data): array {
        $list = InvoiceOutModel::select("invoice_outs.*",
        "customers.name as customer_name",
        "orders.status as order_status",
        DB::raw("
        CASE
            WHEN shippings.shipping_fee_actual > 0
                THEN shipping_fee_actual
            ELSE shipping_fee_estimated
        END AS shipping_fee
        "))
        ->join('orders','orders.id','=','invoice_outs.order_id')
        ->join('shippings','shippings.order_id','=','orders.id')
        ->join('customers','customers.id','=','orders.customer_id')
        ->where('invoice_outs.business_id',$data['business_id']);
        if(!empty($data['payment_status'])) {
            $list = $list->where('invoice_outs.payment_status',$data['payment_status']);
        }
        if(!empty($data['keywords'])) {
            $list = $list->where('invoice_outs.document_no',$data['keywords']);
        }
        return $list->orderBy("invoice_outs.id",$data['order_by'])->paginate(15)->toArray();
    }
    public function update(InvoiceOut $entity): InvoiceOut
    {
        InvoiceOutModel::where('id',$entity->id)
        ->where('business_id',$entity->business_id)->update($entity->toArray());
        return $entity;
    }
    public function findWithFullData(array $data): ?array
    {
        return InvoiceOutModel::select("invoice_outs.*",
        "customers.name as customer_name",
        "customers.email as email",
        "customers.address as address",
        "customers.tax_code as tax_code",
        "customers.phone as phone",
        "orders.payment_method as payment_method",
        "orders.note as note",
        "shippings.receiver_name as receiver_name",
        "shippings.receiver_phone as receiver_phone",
        "shippings.receiver_address as receiver_address",
        "shippings.receiver_note as receiver_note",
        "shippings.shipping_fee_estimated as shipping_fee_estimated",
        "shippings.shipping_unit as shipping_unit",
        "shippings.shipping_code as shipping_code",
        "shippings.shipping_fee_actual as shipping_fee_actual",
        "shippings.shipping_fee_actual as shipping_fee_actual",
        DB::raw("
        CASE
            WHEN shippings.shipping_fee_actual > 0
                THEN shipping_fee_actual
            ELSE shipping_fee_estimated
        END AS shipping_fee
        "),
        "shipping_providers.name as preferred_unit_name",
        "orders.type as type")
        ->join('orders','orders.id','=','invoice_outs.order_id')
        ->join('customers','customers.id','=','orders.customer_id')
        ->join('shippings','shippings.order_id','=','orders.id')
        ->join('shipping_providers','shipping_providers.id'
        ,'=','shippings.preferred_unit')
        ->where('invoice_outs.id',$data['id'])
        ->where('invoice_outs.business_id',$data['business_id'])
        ->first()?->toArray();
    }
}