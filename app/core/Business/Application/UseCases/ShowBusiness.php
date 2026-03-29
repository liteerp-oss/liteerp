<?php

namespace Core\Business\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\AppToken\Application\DTOs\CreateAppTokenRequest;
use Core\AppToken\Application\UseCases\CreateAppToken;
use Core\Authencation\Application\UseCases\ProfileAuthencation;
use Core\Business\Application\DTOs\ShowBusinessRequest;
use Core\Business\Domain\Services\BusinessService;

class ShowBusiness
{
    public function __construct(
        private BusinessService $service,
        private ProfileAuthencation $profile,
        private CreateAppToken $createAppToken,
        private HookDispatcher $hook
    ) {}
    public function handle(array $data)
    {
        $dto = ShowBusinessRequest::fromArray($data);
        $data = $this->hook->dispatch(new HookContext(
            action: HookAction::SHOW,
            phase: HookPhase::RESPONSE,
            timing: HookTiming::BEFORE,
            module: 'Business',
            payload: [
                ...$data,
                ...$dto->toArray()
            ]
        ));
        $user = $this->profile->handle();
        $business = $this->service->show([
            'business_id' => $dto->id,
            'user_id' => $user->id
        ]);
        $token = $this->createAppToken->handle(CreateAppTokenRequest::fromArray([
            'id' => $business['id'],
            'data' => [
                ...$business,
                'user_id' => $user->id,
                'username' => $user->name
            ],
            'exp' => config('business.exp_token')
        ]));
        $data = $this->hook->dispatch(new HookContext(
            action: HookAction::SHOW,
            phase: HookPhase::RESPONSE,
            timing: HookTiming::AFTER,
            module: 'Business',
            payload: [
                ...$data,
                ...$user->toArray(),
                'user_id' => $user->id,
                ...$business,
                'currency' => config('business.currency'),
                'currency_locale'   => config('business.currency_locale')
            ]
        ));
        return [
            'business' => [
                ...$data
            ],
            'token' => $token
        ];
    }
}
