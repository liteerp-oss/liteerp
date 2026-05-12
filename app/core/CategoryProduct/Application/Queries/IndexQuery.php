<?php

namespace Core\CategoryProduct\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\CategoryProductModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\CategoryProduct\Application\DTOs\IndexCategoryProductRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface {
    function __construct(private HookDispatcher $hooks)
    {

    }
    public function handle(array $data): array
    {
        $dto = IndexCategoryProductRequest::fromArray($data);
        $index = CategoryProductModel::select("category_product.*","users.name as created_by_name")
        ->join("users","users.id","=","category_product.created_by")
        ->where('category_product.business_id',$dto->business_id);
        $hooks = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => $data,
                    'query' => $index
                ],
                module: 'CategoryProduct'
            )
        );
        $data = $hooks['data'];
        $index = $hooks['query'];
        if(!empty($dto->keywords)) {
            $index = $index->whereAny(['category_product.name', 'category_product.description'], 'like', '%' . $dto->keywords . '%');
        }
        // Event::dispatch(Permission::CATEGORYPRODUCT_INDEX->value, [
        //     ...$data
        // ]);
        return $index->orderBy('category_product.id', $dto->order_by)->paginate(15)->toArray();
    }
}
