<?php

namespace Core\PermissionGroup\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\PermissionGroupModel;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PermissionGroup\Application\DTOs\IndexPermissionGroupRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    public function __construct(private HookDispatcher $hooks) {}

    public function handle(array $data): array
    {
        $dto = IndexPermissionGroupRequest::fromArray($data);

        $rows = PermissionGroupModel::select("permission_groups.*","users.name as created_by_name")
        ->join("users", "users.id", "permission_groups.user_id")
        ->where('permission_groups.business_id',$data['business_id']);
        $hookPayload = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $rows,
                    'data' => [
                        ...$data,
                        ...$dto->toArray(),
                    ],
                ],
                module: 'PermissionGroup'
            )
        );
        $rows = $hookPayload['query'];
        $data = $hookPayload['data'];
        if($dto->keywords) {
            $rows = $rows->whereAny(['permission_groups.name','permission_groups.type'], 'like', '%' . $dto->keywords . '%');
        }
        Event::dispatch(Permission::PERMISSIONGROUP_INDEX->value, [
            ...$data
        ]);
        return $rows->orderBy('id', $dto->order_by)->paginate(15)->toArray();
    }
}
