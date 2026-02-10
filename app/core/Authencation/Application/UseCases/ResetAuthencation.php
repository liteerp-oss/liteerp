<?php

namespace Core\Authencation\Application\UseCases;

use Core\AppToken\Application\UseCases\ParseAppToken;
use Core\Authencation\Application\DTOs\ResetAuthencationRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ResetAuthencation
{
    public function __construct(private AuthencationService $service,
    private ParseAppToken $parseAppToken) {}

    public function handle(ResetAuthencationRequest $data)
    {
        $tokenData = $this->parseAppToken->handle($data->token);
        $newPassword = Str::random(8);
        $account = $this->service->resetPassword([
            'id' => $tokenData->data->id,
            'password' => Hash::make($newPassword)
        ]);
        Event::dispatch('erp.notification.create', [
            'user_id' => $account->id,
            'message' => __("authencation::messages.changed_password",[
                'password' => $newPassword
            ]),
            'title'   => __("authencation::messages.security_account"),
            'entity_type' => "users",
            'entity_id' => $account->id,
            'chanels' => ['mail'],
            'link'     => URL::to('/dashboard/login')
        ]);
        return [];
    }
}