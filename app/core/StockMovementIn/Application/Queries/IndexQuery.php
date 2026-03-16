<?php

namespace Core\StockMovementIn\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\StockMovementInModel;
use Core\StockMovementIn\Application\DTOs\IndexStockMovementInRequest;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    public function __construct(private HookDispatcher $hooks) {}
    public function handle(array $data): array
    {
        $dto = IndexStockMovementInRequest::fromArray($data);
        $rows = StockMovementInModel::select(
            "stock_movements_in.id",
            "stock_movements_in.id as stock_movements_in_id",
            "suppliers.unit_name as unit_name",
            "products.name as name",
            "products.unit as unit",
            "products.sku as sku",
            "category_product.name as category",
            "category_product.tax",
            "warehouses.name as warehouse",
            "purchases.id as purchase_id",
            "purchases.purchase_date as purchase_date"
        )
            ->join(
                "stock_ins",
                "stock_ins.id",
                "=",
                "stock_movements_in.stock_in_id"
            )
            ->join(
                "invoice_ins",
                "invoice_ins.id",
                "=",
                "stock_ins.invoice_in_id"
            )
            ->join(
                "products",
                "products.id",
                "=",
                "stock_movements_in.product_id"
            )
            ->join(
                "warehouses",
                "warehouses.id",
                "=",
                "stock_movements_in.warehouse_id"
            )
            ->join(
                "purchases",
                "purchases.id",
                "=",
                "invoice_ins.purchase_id"
            )
            ->join(
                "suppliers",
                "suppliers.id",
                "=",
                "purchases.supplier_id"
            )
            ->join(
                "category_product",
                "category_product.id",
                "=",
                "products.category_id"
            );
        if ($dto->customer_id) {
            $rows = $rows->join("price_list", "price_list.product_id", "=", "products.id")
                ->join("customer_group", "customer_group.id", "=", "price_list.customer_group_id")
                ->join("customers", "customers.group", "=", "customer_group.id")
                ->leftJoin(
                    "order_items",
                    "order_items.stock_movements_in_id",
                    "=",
                    "stock_movements_in.id"
                )
                ->groupBy(
                    "stock_movements_in.id",
                    "suppliers.unit_name",
                    "products.name",
                    "products.unit",
                    "products.sku",
                    "category_product.name",
                    "warehouses.name",
                    "purchases.id",
                    "price_list.id"
                )
                ->addSelect(DB::raw("stock_movements_in.qty_change - COALESCE(SUM(
                        order_items.buy_quantity 
                        + order_items.gift_quantity
                        + order_items.compensation_quantity
                        + order_items.conversion_quantity
                    ),0) as quantity"),
                    "price_list.price")
                ->where('customers.id', $dto->customer_id)
                ->where('order_items.deleted_at', NULL)
                ->where(function($query) {
                    $query->where('order_items.cancelled', NULL)
                    ->orWhere('order_items.cancelled', false);
                })
                ->havingRaw("quantity > 0");
        }
        $rows = $rows->where('invoice_ins.business_id', $dto->business_id);
        if($dto->stock_in_id) {
            $rows = $rows->where('stock_movements_in.stock_in_id', $dto->stock_in_id);
        }
            
        if ($dto->keywords) {
            $rows->whereAny(
                ['products.name', 'products.sku', 'category_product.name'],
                'like',
                '%' . $dto->keywords . '%'
            );
        }
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
                    'query' => $rows
                ],
                module: 'StockMovementIn'
            )
        );
        $rows = $data['query'];
        $data = $data['data'];
        Event::dispatch(Permission::STOCKMOVEMENTIN_INDEX->value, [
            ...$data
        ]);
        return $rows->orderBy('stock_movements_in.id', $dto->order_by)->paginate(15)->toArray();
    }
}
