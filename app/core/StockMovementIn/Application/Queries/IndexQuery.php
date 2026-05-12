<?php

namespace Core\StockMovementIn\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\InventoryAdjustmentModel;
use App\Models\OrderItemModel;
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
        /**
         * Sub query InventoryAdjustmentModel
         */
        $iaSub = InventoryAdjustmentModel::select(
            "inventory_adjustments.stock_movements_in_id",
            DB::raw("SUM(inventory_adjustments.qty_adjusted) as total_adjustment")
        )->groupBy("inventory_adjustments.stock_movements_in_id");
        /**
         * Sub query OrderItemModel
         */
        $oiSub = OrderItemModel::select(
            "order_items.stock_movements_in_id",
            DB::raw("SUM(
            order_items.buy_quantity 
                        + order_items.gift_quantity
                        + order_items.compensation_quantity
                        + order_items.conversion_quantity
            ) as total_order_qty")
        )
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('order_items.cancelled', false)
                    ->orWhere('order_items.cancelled', NULL);
            })
            ->groupBy('stock_movements_in_id');
        /**
         * Mail query 
         */
        $rows = StockMovementInModel::select(
            "stock_movements_in.*",
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
            )->leftJoinSub($oiSub, 'oi', function ($join) {
                $join->on('oi.stock_movements_in_id', '=', 'stock_movements_in.id');
            })
            ->leftJoinSub($iaSub, 'ia', function ($join) {
                $join->on('ia.stock_movements_in_id', '=', 'stock_movements_in.id');
            });
        /**
         * Take order 
         * Inventory adjustment
         */
        if ($dto->customer_id || $dto->purchase_id) {
            $rows = $rows->addSelect(
                DB::raw('
                        stock_movements_in.qty_change 
                        - COALESCE(oi.total_order_qty, 0)
                        + COALESCE(ia.total_adjustment, 0) as quantity
                    ')
            )->whereRaw('
                    stock_movements_in.qty_change 
                    - COALESCE(oi.total_order_qty, 0)
                    + COALESCE(ia.total_adjustment, 0) > 0
                ');
            /**
             * Search for take order 
             */
            if ($dto->customer_id) {
                $rows = $rows->join("price_list", "price_list.product_id", "=", "products.id")
                    ->join("customer_group", "customer_group.id", "=", "price_list.customer_group_id")
                    ->join("customers", "customers.group", "=", "customer_group.id")
                    ->addSelect("price_list.price")
                    ->where('customers.id', $dto->customer_id)
                    ->where('stock_ins.status', 'received');
            }
            /**
             * For inventory adjustment 
             */
            else if ($dto->purchase_id) {
                $rows = $rows
                    ->where('purchases.id', $dto->purchase_id)
                    ->where('stock_ins.status', 'received');
            }
        }
        $rows = $rows->where('invoice_ins.business_id', $dto->business_id);
        if ($dto->stock_in_id) {
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
        // Event::dispatch(Permission::STOCKMOVEMENTIN_INDEX->value, [
        //     ...$data
        // ]);
        return $rows->orderBy('stock_movements_in.id', $dto->order_by)->paginate(15)->toArray();
    }
}
