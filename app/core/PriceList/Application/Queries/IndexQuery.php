<?php

namespace Core\PriceList\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\PriceListModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PriceList\Application\DTOs\IndexPriceListRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(private HookDispatcher $hooks) {}
    public function handle(array $data): array
    {
        $dto = IndexPriceListRequest::fromArray($data);
        $rows = PriceListModel::select(
            "price_list.*",
            "products.name as name",
            "customer_group.name as group",
            "category_product.tax as tax"
        )
            ->join("products", "products.id", "=", "price_list.product_id")
            ->join(
                "customer_group",
                "customer_group.id",
                "=",
                "price_list.customer_group_id"
            )
            ->join(
                "category_product",
                "category_product.id",
                "=",
                "products.category_id"
            )
            ->where("products.business_id", $dto->business_id);
        $hooks = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $rows,
                    'data' => $data
                ],
                module: 'PriceList'
            )
        );
        $rows = $hooks['query'];
        $data = $hooks['data'];
        if($dto->keywords) {
            $rows = $rows->whereAny(['products.name','customer_group.name'],
            'like', '%' . $dto->keywords . '%');
        }
        // Event::dispatch(Permission::PRICELIST_INDEX->value, [
        //     ...$data
        // ]);
        return $rows->orderBy('price_list.id', $dto->order_by)->paginate(15)->toArray();
    }
}