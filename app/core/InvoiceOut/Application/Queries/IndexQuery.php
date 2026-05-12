<?php

namespace Core\InvoiceOut\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\InvoiceOutModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InvoiceOut\Application\DTOs\IndexInvoiceOutRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(private HookDispatcher $hooks) {}
    function handle(array $data): array
    {
        $dto = IndexInvoiceOutRequest::fromArray($data);
        $list = InvoiceOutModel::select(
            "invoice_outs.*",
            "customers.name as customer_name",
            "orders.status as order_status",
            DB::raw("
        CASE
            WHEN shippings.shipping_fee_actual > 0
                THEN shipping_fee_actual
            ELSE shipping_fee_estimated
        END AS shipping_fee
        "),
            DB::raw("
        CASE
            WHEN shippings.shipping_fee_actual > 0
                THEN (total - shippings.shipping_fee_estimated + shippings.shipping_fee_actual)
            ELSE total
        END AS total_adjusted
        ")
        )
            ->join('orders', 'orders.id', '=', 'invoice_outs.order_id')
            ->join('shippings', 'shippings.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('invoice_outs.business_id', $dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => [
                        ...$data,
                        ...$dto->toArray()
                    ],
                    'query' => $list
                ],
                module: 'InvoiceOut'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $list = $list->whereAny(['invoice_outs.document_no',
            'customers.name',
            'customers.email'], 'like', '%' . $dto->keywords . '%');    
        }
        // Event::dispatch(Permission::INVOICEOUT_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy("invoice_outs.id", $dto->order_by)->paginate(15)->toArray();
    }
}
