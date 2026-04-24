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

class UpdateInvoiceIn
{
    public function __construct(
        private InvoiceInService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateInvoiceInRequest::fromArray($data);
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
        $entity = $this->service->findById($dto->toArray());
        $update = $this->service->update($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'InvoiceIn'
            )
        );
        $status = 'updated';
        if($update->isApproved() && !$entity->isApproved()) {
            Event::dispatch(Permission::INVOICEIN_APPROVED->value, [
                ...$data,
                'invoice_in_id' => $update->id,
            ]);
            $status = 'approved';
        } else {
            Event::dispatch(Permission::INVOICEIN_UPDATE->value, [
                ...$data,
                'invoice_in_id' => $update->id,
            ]);
        }
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $status,
            'entity_type' => 'invoicein',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'title' => "invoicein::messages.title",
            'message' => "invoicein::messages.notification.$status",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::INVOICEIN_APPROVED,
                Permission::INVOICEIN_UPDATE,
                Permission::PURCHASE_APPROVED,
                Permission::PURCHASE_CANCELLED
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'invoicein',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $update;
    }
}
