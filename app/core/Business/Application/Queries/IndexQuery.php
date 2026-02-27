<?php 
namespace Core\Business\Application\Queries;

use App\Contracts\Queries\QueryInterface;
use App\Models\BusinessModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Business\Application\DTOs\IndexBusinessRequest;

class IndexQuery implements QueryInterface {
    public function __construct(private HookDispatcher $hook)
    {
        
    }
    public function handle(array $data): array
    {
        $dto = IndexBusinessRequest::fromArray($data);
        $list = BusinessModel::select("business.*")
        ->join("permission_groups","permission_groups.business_id","=","business.id")
        ->join("permission_group_user","permission_groups.id","=","permission_group_user.group_id")
        ->where('permission_group_user.account_id',$dto->user_id);
        $data = $this->hook->dispatch(new HookContext(
            action: HookAction::INDEX,
            phase: HookPhase::QUERY,
            timing: HookTiming::ON,
            module: 'Business',
            payload: [
                'data' => [
                    ...$data,
                    ...$dto->toArray()
                ],
                'query' => $list
            ]
        ));
        $list = $data['query'];
        $data = $data['data'];
        $list = $list->groupBy("business.id");
        return $list->limit(50)->get()->toArray();
    }
}