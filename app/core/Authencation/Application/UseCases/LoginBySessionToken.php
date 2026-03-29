<?php

namespace Core\Authencation\Application\UseCases;

use Core\AppToken\Application\UseCases\ParseAppToken;
use Core\Authencation\Application\DTOs\WebSessionTokenRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Core\Authencation\Domain\Services\AuthSessionManager;
use Illuminate\Support\Facades\URL;

class LoginBySessionToken
{
    public function __construct(private ParseAppToken $parseAppToken,
    private AuthSessionManager $authSessionManager) {}

    public function handle(array $data)
    {
        $this->authSessionManager->logout();
        $dto = WebSessionTokenRequest::fromArray($data);
        $tokenData = $this->parseAppToken->handle($dto->token);
        if($tokenData?->data?->id && $tokenData?->data?->type === 'web_login') {
             $this->authSessionManager->login([
                'id' => $tokenData->data->id
             ]);
             return true;
        }
        return abort(403);
    }
}