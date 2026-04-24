<?php

namespace Core\Shipping\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Shipping\Application\DTOs\DeleteShippingRequest;
use Core\Shipping\Domain\Services\ShippingService;
use Illuminate\Support\Facades\Event;

class DeleteShipping
{
    public function __construct(private ShippingService $service,
    private HookDispatcher $dispatch) {}

    public function handle(array $data)
    {
        $dto = DeleteShippingRequest::fromArray($data);
        $data = $this->dispatch->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Shipping'
            )
        );
        $delete = $this->service->delete($dto->toArray());
        $data = $this->dispatch->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$delete->toArray()
                ],
                module: 'Shipping'
            )
        );
        Event::dispatch(Permission::SHIPPING_DELETE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.delete', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'shipping',
            'id' => $delete->id,
            'data' => $delete->toArray()        
        ]);
        return $data;
    }
}