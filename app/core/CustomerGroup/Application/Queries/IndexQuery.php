<?php 
namespace Core\CustomerGroup\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Models\CustomerGroupModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\CustomerGroup\Application\DTOs\IndexCustomerGroupRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery
{
    public function __construct(private HookDispatcher $hooks) {}
    function handle(array $data): array
    {
        $dto = IndexCustomerGroupRequest::fromArray($data);
        $list = CustomerGroupModel::select("customer_group.*")->where('customer_group.business_id',$dto->business_id);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                module: 'CustomerGroup',
                timing: HookTiming::ON,
                phase: HookPhase::QUERY,
                payload: [
                    'query' => $list,
                    'data' => $data
                ]
            )
        );
        $list = $data['query'];
        $data = $data['data'];
        $list = $list->where('customer_group.name', 'like', '%' . $dto->keywords . '%');
        $list = $list->orderBy('id', $dto->order_by ?? 'DESC');
        // Event::dispatch(Permission::CUSTOMERGROUP_INDEX->value, [
        //     ...$data
        // ]);
        return $list->paginate(15)->toArray();
    }
}