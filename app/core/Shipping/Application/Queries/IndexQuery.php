<?php

namespace Core\Shipping\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\ShippingProviderModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Shipping\Application\DTOs\IndexShippingRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(private HookDispatcher $dispatch) {}
    public function handle(array $data): array
    {
        $dto = IndexShippingRequest::fromArray($data);
        $index = ShippingProviderModel::select("shipping_providers.*")
            ->where('shipping_providers.business_id', $dto->business_id);
        $hooks = $this->dispatch->dispatch(
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
                module: 'Shipping'
            )
        );
        $data = $hooks['data'];
        $index = $hooks['query'];
        if ($dto->keywords) {
            $index->whereAny(['shipping_providers.name','shipping_providers.code'], 'like', '%' . $dto->keywords . '%');
        }
        if($dto->active) {
            $index->where('shipping_providers.active', $dto->active);
        }
        // Event::dispatch(Permission::SHIPPING_INDEX->value, [
        //     ...$data
        // ]);
        return $index->orderBy('shipping_providers.id', $dto->order_by)->paginate(15)->toArray();
    }
}
