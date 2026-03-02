<?php 
namespace Core\Inventory\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Hooks\HookDispatcher;
use App\Contracts\Queries\QueryInterface;
use App\Models\InventoryModel;
use Core\Inventory\Application\DTOs\IndexInventoryRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    public function __construct(private HookDispatcher $hooks) {}
    public function handle(array $data): array
    {
        $dto = IndexInventoryRequest::fromArray($data);
        $index = InventoryModel::select(
            "inventories.*",
            "products.name as name",
            "products.sku as sku",
            "products.unit as unit",
            "warehouses.name as warehouse",
            "category_product.name as category",
            "category_product.tax as tax"
        )
            ->join("products", "products.id", "=", "inventories.product_id")
            ->join("warehouses", "warehouses.id", "=", "inventories.warehouse_id")
            ->join(
                "category_product",
                "category_product.id",
                "=",
                "products.category_id"
            )->where('products.business_id', $dto->business_id);
        if($dto->order_id) {
            $index = $index->addSelect("price_list.price as price")
            ->join("price_list", "price_list.product_id", "=", "products.id")
            ->join("customer_group", "customer_group.id", "=", "price_list.customer_group_id")
            ->join("customers", "customers.group", "=", "customer_group.id")
            ->join("orders", "orders.customer_id", "=", "customers.id")
            ->where('orders.id', $dto->order_id)
            ->groupBy("price_list.price");
        }
        $index = $index->groupBy(
            "inventories.id",
            "products.id"
        );
        $hooks = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $index,
                    'data' => [
                        ...$data,
                        ...$dto->toArray()
                    ]
                ],
                module: 'Inventory'
            )
        );
        $data = $hooks['data'];
        $index = $hooks['query'];
        if ($dto->keywords) {
            $index = $index->whereAny(
                ['products.name','products.sku','products.unit'],
                'like',
                '%' . $dto->keywords . '%'
            );
        }
        Event::dispatch(Permission::INVENTORY_INDEX->value, [
            ...$data
        ]);
        $index = $index->orderBy('inventories.id', $dto->order_by)->paginate(15)->toArray();
        return $index;
    }
}