<?php 
namespace Core\Inventory\Application\Queries;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Hooks\HookDispatcher;
use App\Contracts\Queries\QueryInterface;
use App\Models\InventoryModel;
use Core\Inventory\Application\DTOs\IndexInventoryRequest;

class IndexQuery implements QueryInterface {
    public function __construct(private HookDispatcher $hooks) {}
    public function handle(array $data): array
    {
        $dto = IndexInventoryRequest::fromArray($data);
        $data = $dto->toArray();
        $index = InventoryModel::select(
            "inventories.*",
            "products.name as name",
            "products.sku as sku",
            "products.unit as unit",
            "warehouses.name as warehouse",
            "category_product.name as category",
            "category_product.tax as tax",
            "price_list.price as price"
        )
            ->join("products", "products.id", "=", "inventories.product_id")
            ->join("warehouses", "warehouses.id", "=", "inventories.warehouse_id")
            ->join(
                "category_product",
                "category_product.id",
                "=",
                "products.category_id"
            )
            ->join("price_list", "price_list.product_id", "=", "products.id");
        $index = $index->groupBy(
            "inventories.id",
            "products.id",
            "price_list.price"
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
        if (!empty($data['keywords'])) {
            $index = $index->whereAny(
                ['products.name','products.sku','products.unit'],
                'like',
                '%' . $data['keywords'] . '%'
            );
        }
        $index = $index->orderBy('inventories.id', $data['order_by'])->paginate(15)->toArray();
        return $index;
    }
}