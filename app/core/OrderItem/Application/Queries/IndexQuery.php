<?php 
namespace Core\OrderItem\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Models\OrderItemModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\OrderItem\Application\DTOs\IndexOrderItemRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery
{
    public function __construct(private HookDispatcher $hooks ) {}

    public function handle(array $data)
    {
        $dto = IndexOrderItemRequest::fromArray($data);
        $list = OrderItemModel::select(
            "order_items.*",
            "products.name",
            "products.sku",
            "products.unit",
            "products.image",
            "order_items.price as price",
            "warehouses.name as warehouse",
            "order_items.tax as tax",
            "purchases.id as purchase_id",
            DB::raw("
                ROUND(
                (order_items.price * order_items.buy_quantity) * (order_items.discount / 100)
                ,2)
                as total_discount 
            "),
            DB::raw("ROUND(
                (
                    (order_items.price * order_items.buy_quantity)
                    -
                    (
                        (order_items.price * order_items.buy_quantity) * (order_items.discount / 100)
                    )
                ) 
                * (order_items.tax / 100),2) as total_tax"),
            DB::raw("ROUND(order_items.price * order_items.buy_quantity,2) as subtotal"),
            DB::raw("ROUND(
                (order_items.price * order_items.buy_quantity)
                -
                (order_items.price * order_items.buy_quantity * order_items.discount / 100)
                + 
                (
                    (
                        (order_items.price * order_items.buy_quantity)
                        -
                        (
                            (order_items.price * order_items.buy_quantity
                                * order_items.discount / 100)
                        )
                    ) 
                    * (order_items.tax / 100)
                )
                ,2) as total")
        )
            ->join("stock_movements_in", "stock_movements_in.id", "=", "order_items.stock_movements_in_id")
            ->join("warehouses", "warehouses.id", "=", "stock_movements_in.warehouse_id")
            ->join("products", "products.id", "=", "stock_movements_in.product_id")
            ->join("stock_ins","stock_ins.id","=","stock_movements_in.stock_in_id")
            ->join("invoice_ins","invoice_ins.id","=","stock_ins.invoice_in_id")
            ->join("purchases","purchases.id","=","invoice_ins.purchase_id")
            ->where('products.business_id', $dto->business_id)
            ->where('order_items.order_id', $dto->order_id);
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
                module: 'OrderItem'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if( $dto->keywords) {
            $list = $list->whereAny(['products.name', 'products.sku'],'like', "%{$dto->keywords}%");
        }
        // Event::dispatch(Permission::ORDERITEM_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy('order_items.id', $dto->order_by)
            ->paginate($dto->paginate)->toArray();
    }
}