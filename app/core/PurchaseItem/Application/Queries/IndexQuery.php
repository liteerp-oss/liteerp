<?php 
namespace Core\PurchaseItem\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\PurchaseItemModel;
use Illuminate\Support\Facades\DB;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PurchaseItem\Application\DTOs\IndexPurchaseItemRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    public function __construct(private HookDispatcher $hooks)
    {
        
    }
    public function handle(array $data): array
    {
        $dto = IndexPurchaseItemRequest::fromArray($data);
        $list = PurchaseItemModel::select("purchase_items.*",
        "products.image as image","products.name as name",
        "products.sku as sku","products.unit as unit",
        "category_product.name as category_name",
        "suppliers.unit_name as unit_name",
        "products.id as product_id",
        DB::raw("(purchase_items.buy_quantity 
            + purchase_items.gift_quantity
            + purchase_items.compensation_quantity
            + purchase_items.conversion_quantity) as quantity"),
        DB::raw("(purchase_items.buy_quantity * purchase_items.unit_cost) as subtotal"),
        DB::raw("ROUND(((purchase_items.buy_quantity * unit_cost) * purchase_items.tax / 100),2) as total_tax"),
        DB::raw("ROUND(
            ((purchase_items.buy_quantity * unit_cost) + ((purchase_items.buy_quantity * unit_cost) * purchase_items.tax / 100))
            ,2) 
            as total"))
        ->join("products","products.id","=","purchase_items.product_id")
        ->join("category_product","category_product.id","=","products.category_id")
        ->join("purchases","purchases.id","=","purchase_items.purchase_id")
        ->join("suppliers","suppliers.id","=","purchases.supplier_id")
        ->where('purchase_items.purchase_id', $dto->purchase_id)
        ->where('purchases.business_id', $dto->business_id);
        $hookData = $this->hooks->dispatch(
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
                module: 'PurchaseItem'
            )
        );
        $list = $hookData['query'];
        $data = $hookData['data'];
        if($dto->keywords){
            $list = $list->whereAny(['products.name', 'products.sku', 'category_product.name'],
            'like', "%{$dto->keywords}%");
        }
        // Event::dispatch(Permission::PURCHASEITEM_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy('purchase_items.id', $dto->order_by)->paginate(15)->toArray();
    }
}