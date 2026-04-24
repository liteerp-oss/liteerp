<?php

namespace Core\User\Application\Queries;

use App\Supports\Permissions\Enums\Permission;

use App\Contracts\Queries\QueryInterface;
use App\Models\User;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\User\Application\DTOs\IndexUserRequest;
use Illuminate\Support\Facades\Event;

class IndexQuery implements QueryInterface
{
    function __construct(
        private HookDispatcher $hooks
    ) {}
    function handle(array $data): array
    {
        $dto = IndexUserRequest::fromArray($data);
        $rows = User::select(
            "users.*",
            "permission_groups.name as group",
            "permission_groups.id as group_id",
            "permission_groups.business_id as business_id"
        )
            ->join('permission_group_user',"permission_group_user.account_id","=","users.id")
            ->join("permission_groups", "permission_groups.id", "=", "permission_group_user.group_id")
            ->where('permission_groups.business_id', $dto->business_id);
        if($dto->keywords) {
            $rows = $rows->whereAny(['users.name', 'users.email'], 'like', '%' . $dto->keywords . '%');
        }
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::QUERY,
                timing: HookTiming::ON,
                payload: [
                    'query' => $rows,
                    'data' => [
                        ...$data,
                        ...$dto->toArray()
                    ]
                ],
                module: 'User'
            )
        );
        $rows = $data['query'];
        $data = $data['data'];
        Event::dispatch(Permission::USER_INDEX->value, [
            ...$data
        ]);
        return $rows->orderBy('users.id', $dto->order_by)->paginate($dto->paginate ?? 15)->toArray();
    }
}
