<?php

namespace Core\StockOut\Application\Queries;

use App\Contracts\Queries\QueryInterface;
use App\Models\StockOutModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\StockOut\Application\DTOs\IndexStockOutRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    public function __construct(private HookDispatcher $hooks) {}
    function handle(array $data): array
    {
        $dto = IndexStockOutRequest::fromArray($data);
        $index = StockOutModel::select(
            "stock_outs.*",
            DB::raw("SUM(order_items.buy_quantity) + SUM(order_items.gift_quantity) 
            + SUM(order_items.compensation_quantity) 
            + SUM(order_items.conversion_quantity) as quantity"),
            "orders.expected_delivery_date as expected_delivery_date",
            "orders.order_date as order_date",
            "orders.status as order_status",
            "orders.id as order_id",
            "invoice_outs.document_no as document_no",
            "customers.name as customer_name",
            "shippings.shipping_fee_actual as shipping_fee_actual",
            "shippings.shipping_fee_estimated as shipping_fee_estimated",
            DB::raw("
            CASE
                WHEN shippings.shipping_fee_actual > 0
                    THEN shippings.shipping_fee_actual
                ELSE shippings.shipping_fee_estimated
            END AS shipping_fee
            ")
        )
            ->join("invoice_outs", "invoice_outs.id", "=", "stock_outs.invoice_out_id")
            ->join("orders", "orders.id", "=", "invoice_outs.order_id")
            ->join('shippings', 'shippings.order_id', '=', 'orders.id')
            ->join("order_items", "order_items.order_id", "=", "orders.id")
            ->join("stock_movements_in", "stock_movements_in.id", "=", "order_items.stock_movements_in_id")
            ->join("products", "products.id", "=", "stock_movements_in.product_id")
            ->join("customers", "customers.id", "=", "orders.customer_id")
            ->groupBy("stock_outs.id")
            ->where('stock_outs.business_id', $dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => $data,
                    'query' => $index
                ],
                module: 'StockOut'
            )
        );
        $index = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $index = $index->whereAny(
                ['invoice_outs.document_no', 'customers.name', 'products.name'],
                'like',
                '%' . $dto->keywords . '%'
            );
        }
        // Event::dispatch(Permission::STOCKOUT_INDEX->value, [
        //     ...$data
        // ]);
        return $index->orderBy("stock_outs.id", $dto->order_by)->paginate(15)->toArray();
    }
}
