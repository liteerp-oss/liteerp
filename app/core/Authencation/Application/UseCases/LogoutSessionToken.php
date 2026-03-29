<?php

namespace Core\Authencation\Application\UseCases;

use Core\AppToken\Application\UseCases\ParseAppToken;
use Core\Authencation\Application\DTOs\WebSessionTokenRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Core\Authencation\Domain\Services\AuthSessionManager;
use Illuminate\Support\Facades\URL;

class LogoutSessionToken
{
    public function __construct(private AuthSessionManager $authSessionManager) {}

    public function handle(array $data)
    {
        $this->authSessionManager->logout();
    }
}