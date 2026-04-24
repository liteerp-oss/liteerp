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

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = UnapproveInvoiceOutByOrderCancelledRequest::fromArray($data);
        $findInvoice = $this->service->getByOrderId($dto->toArray());
        if ($findInvoice) {
            $update = $this->service->unApproved($findInvoice->toArray());
            Event::dispatch(Permission::INVOICEOUT_UNAPPROVED->value, [
                ...$data,
                ...$dto->toArray(),
                ...$update->toArray(),
                'invoice_out_id' => $update->id
            ]);
        }

        DB::commit();
        return;
    }
}
