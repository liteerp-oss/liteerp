<?php 
namespace Core\InventoryAdjustment\Application\Queries;

use App\Models\InventoryAdjustmentModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InventoryAdjustment\Application\DTOs\IndexInventoryAdjustmentRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery
{
    public function __construct(private HookDispatcher $hooks) {}
    function handle(array $data): array
    {
        $dto = IndexInventoryAdjustmentRequest::fromArray($data);
        $list = InventoryAdjustmentModel::select("inventory_adjustments.*",
             "products.name as product_name",
             "products.sku as product_sku",
             "warehouses.name as warehouse",
             "users.name as created_by"
             )
            ->join("products", "products.id", "=", "inventory_adjustments.product_id")
            ->join("warehouses", "warehouses.id", "=", "inventory_adjustments.warehouse_id")
            ->join("users", "users.id", "=", "inventory_adjustments.adjusted_by")
            ->where('products.business_id',$dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                module: 'InventoryAdjustment',
                timing: HookTiming::ON,
                phase: HookPhase::QUERY,
                payload: [
                    'query' => $list,
                    'data' => [
                        ...$data,
                        ...$dto->toArray()
                    ]
                ]
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $list = $list->whereAny(['inventory_adjustments.reason', 'products.name', 'products.sku'], 
                    'LIKE', '%' . $data['keywords'] . '%');
        }
        Event::dispatch("erp.inventoryadjustment.index", [
            ...$data
        ]);
        return $list->orderBy('id',$dto->order_by)->paginate()->toArray();
    }
}