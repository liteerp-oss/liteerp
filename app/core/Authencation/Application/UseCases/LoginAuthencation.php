<?php

namespace Core\Authencation\Application\UseCases;

use App\Exceptions\UnauthorizedException;
use App\Supports\Permissions\Enums\Permission;
use Core\AppToken\Application\DTOs\CreateAppTokenRequest;
use Core\AppToken\Application\UseCases\CreateAppToken;
use Core\Authencation\Application\DTOs\CreateAuthencationRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class LoginAuthencation
{
    public function __construct(
        private AuthencationService $service,
        private CreateAppToken $createAppToken
    ) {}

    public function handle(CreateAuthencationRequest $dto)
    {
        $account = $this->service->login($dto->toArray());
        if (!$account->email_verified_at) {
            $token = $this->createAppToken->handle(CreateAppTokenRequest::fromArray([
                'id' => $account->id,
                'data' => [
                    'id' => $account->id,
                    'name' => $account->name
                ],
                'exp' => 5
            ]));
            $notification = [
                'user_id' => $account->id,
                'entity_type' => "users",
                'entity_id' => $account->id,
                'chanels' => ['mail'],
                'message' => "authencation::messages.message_verify_account",
                'title'   => "authencation::messages.subject_verify_account",
                'link'     => URL::to('/dashboard/verify-account?token=' . $token)
            ];
            Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
            throw new UnauthorizedException(__("authencation::messages.not_verify"));
        }
        return [
            ...$account->response(),
            'web_token' => $this->createAppToken->handle(CreateAppTokenRequest::fromArray([
                'id' => $account->id,
                'data' => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'type' => 'web_login'
                ],
                'exp' => 1
            ]))
        ];
    }
}
