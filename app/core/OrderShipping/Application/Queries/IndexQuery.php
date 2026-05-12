<?php

namespace Core\OrderShipping\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\ShippingModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\OrderShipping\Application\DTOs\IndexOrderShippingRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(private HookDispatcher $hooks)
    {
        
    }
    function handle(array $data): array
    {
        $dto = IndexOrderShippingRequest::fromArray($data);

        $list = ShippingModel::select(
            "shippings.*",
            "shipping_providers.name as shipping_provider_name"
        )
            ->join("orders", "orders.id", "=", "shippings.order_id")
            ->join(
                "shipping_providers",
                "shipping_providers.id",
                "=",
                "shippings.preferred_unit"
            )
            ->where('orders.business_id', $dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'data' => $data,
                    'query' => $list
                ],
                module: 'OrderShipping'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if($dto->keywords) {
            $list = $list->whereAny(['shippings.tracking_number','shipping_providers.name'],
            'like', '%' . $dto->keywords . '%');
        }
        // Event::dispatch(Permission::ORDERSHIPPING_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy('shippings.id', $dto->order_by)->paginate(15)
            ->toArray();
    }
}
