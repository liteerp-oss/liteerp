<?php

namespace Core\User\Application\Queries;

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
            "business_role.role as role",
            "business_role.business_id as business_id",
            "business.name as business_name",
            "business.address as business_address"
        )
            ->join("business_role", "business_role.user_id", "=", "users.id")
            ->join("business", "business.id", "=", "business_role.business_id")
            ->where('business.id', $dto->business_id);
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
        Event::dispatch("erp.user.index", [
            ...$data
        ]);
        return $rows->orderBy('users.id', $dto->order_by)->paginate(15)->toArray();
    }
}
