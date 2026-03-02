<?php

namespace Core\User\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Exceptions\BadException;
use Core\User\Application\DTOs\CreateUserRequest;
use Core\User\Domain\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
/**
 * This usecase mean is update user role on business
 * It's not update to account
 */
class UpdateUser
{
    public function __construct(private UserService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateUserRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'User'
            )
        );
        $account = $this->service->getByEmail($dto->toArray());
        if (!$account) {
            throw new BadException(__("user::messages.not_exists_on_business"));
        }
        if($dto->created_by === $account->id) {
            throw new BadException(__("user::messages.cannot_change_own_role"));
        }
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$account->toArray(),
                    'account_id' => $account->id
                ],
                module: 'User'
            )
        );
        Event::dispatch(Permission::USER_UPDATE->value, [
            ...$data
        ]);
        DB::commit();
        return $data;
    }
}
