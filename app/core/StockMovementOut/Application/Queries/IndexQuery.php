<?php 
namespace Core\StockMovementOut\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\StockMovementOutModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockMovementOut\Application\DTOs\IndexStockMovementOutRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    public function __construct(private HookDispatcher $hooks){}
    public function handle(array $data): array
    {
        $dto = IndexStockMovementOutRequest::fromArray($data);
        $list = StockMovementOutModel::select(
            "stock_movements_out.*",
            "products.name as name",
            "products.sku as sku",
            "order_items.buy_quantity as buy_quantity",
            "order_items.gift_quantity as gift_quantity",
            "order_items.compensation_quantity as compensation_quantity",
            "order_items.conversion_quantity as conversion_quantity",
            "warehouses.name as warehouse",
            DB::raw("ROUND(order_items.price * order_items.discount / 100,2) as discount"),
            DB::raw("ROUND(order_items.price,2) as price"),
            DB::raw("ROUND(
            (order_items.price + (order_items.price * order_items.tax / 100)) - (order_items.price * order_items.discount / 100)
            ,2) 
                as total"),
            DB::raw("ROUND(order_items.price * order_items.tax / 100,2) as total_tax")
        )
            ->join("products", "products.id", "=", "stock_movements_out.product_id")
            ->join("warehouses", "warehouses.id", "=", "stock_movements_out.warehouse_id")
            ->join("stock_outs", "stock_outs.id", "=", "stock_movements_out.stock_out_id")
            ->join("invoice_outs", "invoice_outs.id", "=", "stock_outs.invoice_out_id")
            ->join("orders", "orders.id", "=", "invoice_outs.order_id")
            ->join("order_items", "order_items.order_id", "=", "orders.id")
            ->join("customers", "customers.id", "=", "orders.customer_id")
            ->join("customer_group", "customer_group.id", "=", "customers.group")
            ->where('products.business_id', $dto->business_id)
            ->where('stock_movements_out.stock_out_id', $dto->stock_out_id);
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
                module: 'StockMovementOut'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        // Event::dispatch(Permission::STOCKMOVEMENTOUT_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy('stock_movements_out.id', $dto->order_by)->paginate(15)->toArray();
    }
}