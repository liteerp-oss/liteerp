<?php

namespace Core\Business\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\Business\Application\DTOs\CreateBusinessRequest;
use Core\Business\Domain\Services\BusinessService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateBusiness
{
    public function __construct(
        private BusinessService $service,
        private HookDispatcher $hook
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateBusinessRequest::fromArray($data);
        $data = $this->hook->dispatch(new HookContext(
            action: HookAction::CREATE,
            phase: HookPhase::RESPONSE,
            timing: HookTiming::BEFORE,
            module: 'Business',
            payload: [
                ...$data,
                ...$dto->toArray()
            ]
        ));
        $business = $this->service->create($dto->toArray());
        $data = $this->hook->dispatch(new HookContext(
            action: HookAction::CREATE,
            phase: HookPhase::RESPONSE,
            timing: HookTiming::AFTER,
            module: 'Business',
            payload: [
                ...$data,
                ...$business->toArray()
            ]
        ));
        Event::dispatch(Permission::BUSINESS_CREATE->value, [
            ...$data,
            'business_id' => $business->id,
            'role_user_id' => $dto->user_id
        ]);
        DB::commit();
        return $data;
    }
}
