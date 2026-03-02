<?php

namespace Core\InvoiceOut\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use Core\InvoiceOut\Application\DTOs\UnapproveInvoiceOutByOrderCancelledRequest;
use Core\InvoiceOut\Domain\Services\InvoiceOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UnapproveInvoiceOutByOrderCancelled
{
    public function __construct(
        private InvoiceOutService $service
    ) {}

    public function handle(UnapproveInvoiceOutByOrderCancelledRequest $dto)
    {
        DB::beginTransaction();
        $findInvoice = $this->service->getByOrderId($dto->toArray());
        if ($findInvoice) {
            $update = $this->service->unApproved($findInvoice->toArray());
            Event::dispatch(Permission::INVOICEOUT_UNAPPROVED->value, [
                ...$update->toArray(),
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                'invoice_out_id' => $update->id
            ]);
        }

        DB::commit();
        return;
    }
}
