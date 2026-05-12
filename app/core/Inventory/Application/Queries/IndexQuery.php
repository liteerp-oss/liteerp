<?php

namespace Core\Inventory\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Hooks\HookDispatcher;
use App\Contracts\Queries\QueryInterface;
use App\Models\InventoryAdjustmentModel;
use App\Models\InventoryModel;
use App\Models\OrderItemModel;
use App\Models\StockMovementInModel;
use Core\Inventory\Application\DTOs\IndexInventoryRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    public function __construct(private HookDispatcher $hooks) {}
    public function handle(array $data): array
    {
        $dto = IndexInventoryRequest::fromArray($data);
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
            "products.name as name",
            "products.unit as unit",
            "products.sku as sku",
            "category_product.name as category",
            "category_product.tax",
            "warehouses.name as warehouse"
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
                "category_product",
                "category_product.id",
                "=",
                "products.category_id"
            )->leftJoinSub($oiSub, 'oi', function ($join) {
                $join->on('oi.stock_movements_in_id', '=', 'stock_movements_in.id');
            })
            ->leftJoinSub($iaSub, 'ia', function ($join) {
                $join->on('ia.stock_movements_in_id', '=', 'stock_movements_in.id');
            })->addSelect(
                DB::raw('
                        SUM(
                            stock_movements_in.qty_change 
                            - COALESCE(oi.total_order_qty, 0)
                            + COALESCE(ia.total_adjustment, 0)
                        ) as quantity
                    ')
            );
        $rows = $rows->where('invoice_ins.business_id', $dto->business_id)
            ->groupBy(
                "stock_movements_in.warehouse_id",
                "stock_movements_in.product_id",
                "products.name",
                "products.unit",
                "products.sku",
                "category_product.name",
                "category_product.tax",
                "warehouses.name"
            );

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
                module: 'Inventory'
            )
        );
        $rows = $data['query'];
        $data = $data['data'];
        // Event::dispatch(Permission::STOCKMOVEMENTIN_INDEX->value, [
        //     ...$data
        // ]);
        return $rows->orderBy('products.name', $dto->order_by)->paginate($dto->paginate ?? 15)->toArray();
    }
}
