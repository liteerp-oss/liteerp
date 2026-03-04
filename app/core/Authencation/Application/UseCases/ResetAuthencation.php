<?php

namespace Core\Authencation\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\AppToken\Application\UseCases\ParseAppToken;
use Core\Authencation\Application\DTOs\ResetAuthencationRequest;
use Core\Authencation\Domain\Services\AuthencationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ResetAuthencation
{
    public function __construct(
        private AuthencationService $service,
        private ParseAppToken $parseAppToken
    ) {}

    public function handle(ResetAuthencationRequest $data)
    {
        $tokenData = $this->parseAppToken->handle($data->token);
        $newPassword = Str::random(8);
        $account = $this->service->resetPassword([
            'id' => $tokenData->data->id,
            'password' => Hash::make($newPassword)
        ]);
        $notification = [
            'user_id' => $account->id,
            'entity_type' => "users",
            'entity_id' => $account->id,
            'chanels' => ['mail'],
            'message' => "authencation::messages.changed_password",
            'message_params' => [
                'password' => $newPassword
            ],
            'title'   => "authencation::messages.security_account",
            'link'     => URL::to('/dashboard/login'),
            'locate' => App::getLocale()
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        return [];
    }
}
