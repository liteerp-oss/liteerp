<?php

namespace Core\User\Application\UseCases;

use App\Exceptions\BadException;
use Core\User\Application\DTOs\DeleteUserRequest;
use Core\User\Domain\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteUser
{
    public function __construct(private UserService $service) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteUserRequest::fromArray($data);
        $account = $this->service->findById($dto->toArray());
        if($dto->created_by === $account->id) {
            throw new BadException(__("user::messages.cannot_delete_self"));
        }
        Event::dispatch("erp.user.delete", [
            ...$account->toArray(),
            'role_user_id'   => $account->id,
            'user_id'   => $dto->created_by,
            'business_id' => $dto->business_id
        ]);
        DB::commit();
        return $account;
    }
}
