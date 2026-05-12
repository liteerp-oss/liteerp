<?php

namespace Core\Customer\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\CustomerModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Customer\Application\DTOs\IndexCustomerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(private HookDispatcher $hooks) {}
    public function handle(array $data): array
    {
        $dto = IndexCustomerRequest::fromArray($data);
        $list = CustomerModel::select("customers.*",
            "customer_group.name as group_name",
            DB::raw("count(orders.id) as total_order"));
        $list = $list->join("customer_group", "customer_group.id", "=", "customers.group");
        $list = $list->leftJoin("orders", "orders.customer_id", "=", "customers.id")
            ->where('customers.business_id', $dto->business_id)
            ->groupBy("customers.id");
        $hooks = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $list,
                    'data' => $data
                ],
                module: 'Customer'
            )
        );
        $list = $hooks['query'];
        if ($dto->keywords) {
            $list = $list->whereAny(['customers.name', 'customers.email','customers.phone'], 'like', '%' . $dto->keywords . '%');
        }
        if($dto->active) {
            $list = $list->where('customers.active', $dto->active);
        }
        // Event::dispatch(Permission::CUSTOMER_INDEX->value, [
        //     ...$data
        // ]);
        return $list->orderBy('customers.id', $dto->order_by)->paginate(15)->toArray();
    }
}
