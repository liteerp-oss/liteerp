<?php

namespace Core\User\Application\UseCases;

use App\Exceptions\BadException;
use Core\User\Application\DTOs\CreateUserRequest;
use Core\User\Domain\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * This usecase mean is update user role on business
 * It's not update to account
 */
class UpdateUser
{
    public function __construct(private UserService $service) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateUserRequest::fromArray($data);
        $account = $this->service->getByEmail($dto->toArray());
        if (!$account) {
            throw new BadException(__("user::messages.not_exists_on_business"));
        }
        if($dto->created_by === $account->id) {
            throw new BadException(__("user::messages.cannot_change_own_role"));
        }
        Event::dispatch("erp.user.update", [
            ...$account->toArray(),
            'role_user_id'   => $account->id,
            'user_id'   => $dto->created_by,
            'business_id' => $dto->business_id,
            'role' => $dto->role
        ]);
        DB::commit();
        return $account;
    }
}
