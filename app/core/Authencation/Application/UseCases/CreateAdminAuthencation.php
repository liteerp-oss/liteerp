<?php

namespace Core\Authencation\Application\UseCases;

use Core\AppToken\Application\UseCases\CreateAppToken;
use Core\Authencation\Application\DTOs\CreateAuthencationRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class CreateAdminAuthencation
{
    public function __construct(private AuthencationService $service) {}

    public function handle(CreateAuthencationRequest $dto)
    {
        DB::beginTransaction();
        $dto->password = Hash::make($dto->password);
        $account = $this->service->createAdmin($dto->toArray());
        Event::dispatch("erp.authencation.create_admin", $account->toArray());
        DB::commit();
        return $account;
    }
}