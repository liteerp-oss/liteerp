<?php

namespace Core\Authencation\Application\UseCases;

use Core\AppToken\Application\DTOs\CreateAppTokenRequest;
use Core\AppToken\Application\UseCases\CreateAppToken;
use Core\Authencation\Application\DTOs\CreateAuthencationRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class CreateAuthencation
{
    public function __construct(private AuthencationService $service,
    private CreateAppToken $createAppToken) {}

    public function handle(CreateAuthencationRequest $dto)
    {
        DB::beginTransaction();
        $dto->password = Hash::make($dto->password);
        $account = $this->service->create($dto->toArray());
        $token = $this->createAppToken->handle(CreateAppTokenRequest::fromArray([
            'id' => $account->id,
            'data' => [
                'id' => $account->id,
                'name' => $account->name
            ],
            'exp' => 5
        ]));
        Event::dispatch('erp.notification.create', [
            'user_id' => $account->id,
            'message' => __("authencation::messages.message_verify_account"),
            'title'   => __("authencation::messages.subject_verify_account"),
            'entity_type' => "users",
            'entity_id' => $account->id,
            'chanels' => ['mail'],
            'link'     => URL::to('/dashboard/verify-account?token=' . $token)
        ]);
        DB::commit();
        return $account;
    }
}