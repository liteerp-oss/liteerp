<?php

namespace Core\InvoiceOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\InvoiceOut\Application\DTOs\CreateInvoiceOutRequest;
use Core\InvoiceOut\Domain\Services\InvoiceOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateInvoiceOut
{
    public function __construct(
        private InvoiceOutService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateInvoiceOutRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'InvoiceOut'
            )
        );
        
        $findInvoice = $this->service->findById($data);
        $update = $this->service->update([
            ...$data,
            // block manual update total price, system will reuse old total price to sure everything is correct
            'total' => $findInvoice->total
        ]);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'InvoiceOut'
            )
        );
        $status = 'updated';
        if($data['approved'] === true && !$findInvoice->isApproved()) {
            Event::dispatch(Permission::INVOICEOUT_APPROVED->value, [
                ...$data,
                'invoice_out_id' => $update->id
            ]);  
            $status = 'approved';
        } else {
            Event::dispatch(Permission::INVOICEOUT_UPDATE->value, [
                ...$data,
                'invoice_out_id' => $update->id
            ]);    
        }
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $status,
            'entity_type' => 'invoiceout',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'title' => "invoiceout::messages.title",
            'message' => "invoiceout::messages.notification.$status",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::INVOICEOUT_CREATE->value,
                Permission::INVOICEOUT_APPROVED->value,
                Permission::INVOICEOUT_DELETE->value,
                Permission::INVOICEOUT_UNAPPROVED->value,
                Permission::INVOICEOUT_UPDATE->value
            ]
        ]);
        
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'invoiceout',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
