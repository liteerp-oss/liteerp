<?php

namespace Core\Warehouse\Application\Queries;

use App\Contracts\Queries\QueryInterface;
use App\Models\WarehouseModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Warehouse\Application\DTOs\IndexWarehouseRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(
        private HookDispatcher $hooks
    ) {}
    function handle(array $data): array
    {
        $dto = IndexWarehouseRequest::fromArray($data);
        $list = WarehouseModel::select("warehouses.*")
        ->where('warehouses.business_id',$dto->business_id);
        if($dto->keywords) {
            $list = $list->whereAny(['warehouses.name','warehouses.address'],'like','%'.$dto->keywords.'%');
        }
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $list,
                    'data' => [
                        ...$data,
                        ...$dto->toArray()
                    ]
                ],
                module: 'Warehouse'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        Event::dispatch("erp.warehouse.index", [
            ...$data
        ]);
        return $list->orderBy('warehouses.id', $dto->order_by)->paginate(15)->toArray();
    }
}
