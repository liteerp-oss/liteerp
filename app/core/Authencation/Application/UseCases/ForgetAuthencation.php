<?php

namespace Core\Authencation\Application\UseCases;

use Core\AppToken\Application\DTOs\CreateAppTokenRequest;
use Core\AppToken\Application\UseCases\CreateAppToken;
use Core\Authencation\Application\DTOs\ForgetAuthencationRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class ForgetAuthencation
{
    public function __construct(
        private AuthencationService $service,
        private CreateAppToken $createAppToken
    ) {}

    public function handle(ForgetAuthencationRequest $dto)
    {
        $account = $this->service->findByEmail($dto->toArray());
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
            'message' => __("authencation::message.message_reset_password"),
            'title'   => __("authencation::message.subject_verify_account"),
            'entity_type' => "users",
            'entity_id' => $account->id,
            'chanels' => ['mail'],
            'link'     => URL::to('/dashboard/reset-password?token=' . $token)
        ]);
        return [];
    }
}
