<?php

namespace Core\PermissionGroupUser\Application\Queries;

use App\Contracts\Queries\QueryInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PermissionGroupUser\Application\DTOs\IndexPermissionGroupUserRequest;
use Illuminate\Support\Facades\DB;

class IndexQuery implements QueryInterface
{
    public function __construct(private HookDispatcher $hooks) {}

    public function handle(array $data): array
    {
        $dto = IndexPermissionGroupUserRequest::fromArray($data);

        $rows = DB::table('permission_group_user')->select('id', 'group_id', 'user_id', 'created_at', 'updated_at');

        if ($dto->group_id) {
            $rows->where('group_id', $dto->group_id);
        }

        if ($dto->user_id) {
            $rows->where('user_id', $dto->user_id);
        }

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
                module: 'PermissionGroupUser'
            )
        );

        if (isset($hookPayload['query'])) {
            $rows = $hookPayload['query'];
        }

        return $rows->orderBy('id', $dto->order_by)->paginate(15)->toArray();
    }
}
