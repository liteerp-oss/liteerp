<?php

namespace Extensions\InvoiceOutPDF\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvoiceOutModel;
use App\Models\OrderItemModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class InvoiceOutPDFController extends Controller
{
    public function show(Request $request, string $invoiceId)
    {
        if ($request->get('lang') && in_array($request->get('lang'), ['vi', 'en', 'ja'])) {
            App::setLocale($request->get('lang'));
        }

        $invoice = InvoiceOutModel::select(
            'invoice_outs.*',
            'orders.order_no',
            'orders.order_date',
            'orders.note as order_note',
            'customers.name as customer_name',
            'customers.address as customer_address',
            'customers.email as customer_email',
            'customers.phone as customer_phone',
            'customers.tax_code as customer_tax_code',
            'shippings.receiver_name',
            'shippings.receiver_phone',
            'shippings.receiver_address',
            'shippings.receiver_note',
            'shippings.shipping_fee_estimated',
            'shippings.shipping_fee_actual',
            DB::raw(
                "CASE
                    WHEN shippings.shipping_fee_actual > 0
                        THEN shippings.shipping_fee_actual
                    ELSE shippings.shipping_fee_estimated
                END AS shipping_fee"
            ),
            DB::raw(
                "CASE
                    WHEN shippings.shipping_fee_actual > 0
                        THEN (invoice_outs.total - shippings.shipping_fee_estimated + shippings.shipping_fee_actual)
                    ELSE invoice_outs.total
                END AS total_adjusted"
            )
        )
            ->join('orders', 'orders.id', '=', 'invoice_outs.order_id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('shippings', 'shippings.order_id', '=', 'orders.id')
            ->where('invoice_outs.id', $invoiceId)
            ->first();

        if (!$invoice) {
            abort(404);
        }

        $items = OrderItemModel::select(
            'order_items.*',
            'products.name as product_name',
            'products.unit as product_unit'
        )
            ->join('inventories', 'inventories.id', '=', 'order_items.inventory_id')
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->where('order_items.order_id', $invoice->order_id)
            ->get();

        return view('InvoiceOutPDF::index', [
            'invoice' => $invoice,
            'items' => $items,
            'lang' => $request->get('lang') ?? 'en',
        ]);
    }
}
