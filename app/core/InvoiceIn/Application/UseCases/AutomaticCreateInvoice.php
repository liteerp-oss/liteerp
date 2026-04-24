<?php

namespace Core\InvoiceIn\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\InvoiceIn\Application\DTOs\CreateInvoiceInRequest;
use Core\InvoiceIn\Domain\Services\InvoiceInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class AutomaticCreateInvoice
{
    public function __construct(private InvoiceInService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateInvoiceInRequest::fromArray($data);
        if($this->service->getByPurchaseId($dto->toArray())) {
            return;
        }
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'InvoiceIn'
            )
        );
        $create = $this->service->create($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray()
                ],
                module: 'InvoiceIn'
            )
        );
        Event::dispatch(Permission::INVOICEIN_CREATE->value,[
            ...$data
        ]);
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'invoicein',
            'entity_id' => $create->id,
            'chanels' => ['db'],
            'title' => "invoicein::messages.title",
            'message' => "invoicein::messages.notification.created",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, [
            ...$notification
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::INVOICEIN_APPROVED,
                Permission::INVOICEIN_UPDATE
            ]
        ]);
        DB::commit();
        return $create;
    }
}