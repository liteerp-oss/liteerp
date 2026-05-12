<?php

namespace Core\Supplier\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\SupplierModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Supplier\Application\DTOs\IndexSupplierRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(
        private HookDispatcher $hooks
    ) {}
    public function handle(array $data): array
    {
        
        $dto = IndexSupplierRequest::fromArray($data);
        $list = SupplierModel::select("suppliers.*")
        ->where('suppliers.business_id', $dto->business_id);
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
                module: 'Supplier'
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        if ($dto->keywords) {
            $list = $list->whereAny(['suppliers.unit_name',
                'suppliers.email',
                'suppliers.phone'], 'like', '%' . $dto->keywords . '%');
        }
        if($dto->active) {
            $list = $list->where('suppliers.active', $dto->active);
        }
        // Event::dispatch(Permission::SUPPLIER_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy('suppliers.id', $dto->order_by)->paginate(15)->toArray();
    }
}
