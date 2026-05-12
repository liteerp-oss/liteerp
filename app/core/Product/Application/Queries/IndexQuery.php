<?php 
namespace Core\Product\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\ProductModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Product\Application\DTOs\IndexProductRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    function __construct(private HookDispatcher $hooks)
    {
        
    }
    function handle(array $data): array
    {
        $dto = IndexProductRequest::fromArray($data);
        Event::dispatch(Permission::PRODUCT_INDEX->value, [
            ...$dto->toArray(),
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id
        ]);
        $rows = ProductModel::select("products.*",
            "category_product.name as category",
            "price_list.price")
            ->join("category_product","category_product.id","=","products.category_id")
            ->leftJoin("price_list","price_list.product_id","=","products.id")
            ->where('products.business_id',$dto->business_id);
        if($dto->keywords) {
            $rows = $rows->whereAny(['products.name', 'products.sku', 'category_product.name'], 
            'like', '%'. $dto->keywords .'%');  
        }
        $hooks = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $rows,
                    'data' => $data
                ],
                module: 'Product'
            )
        );
        $rows = $hooks['query'];
        $data = $hooks['data'];
        // Event::dispatch(Permission::PRODUCT_INDEX->value, [
        //     ...$data
        // ]);
        return $rows->orderBy("products.id",$dto->order_by)
            ->paginate(15)->toArray();
    }
}