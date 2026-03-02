<?php

namespace Core\InvoiceIn\Infrastructure\Listeners;

use Core\InvoiceIn\Application\DTOs\ChangeToUnapprovedRequest;
use Core\InvoiceIn\Application\UseCases\AutomaticCreateInvoice;
use Core\InvoiceIn\Application\UseCases\UnapprovedInvoiceIn;
use Illuminate\Support\Facades\Event;

class InvoiceInListener
{
    public function __construct(
        private AutomaticCreateInvoice $AutomaticCreateInvoice,
        private UnapprovedInvoiceIn $UnapprovedInvoiceIn
    ) {}
    public function handle() {
        Event::listen('erp.purchase.*', function (string $eventName, array $data) {
            if ($eventName === 'erp.purchase.approved') {
                $this->AutomaticCreateInvoice->handle([
                    'purchase_id' => $data['id'],
                    ...$data
                ]);
            } else if ($eventName === 'erp.purchase.cancelled') {
                $this->UnapprovedInvoiceIn->handle(new ChangeToUnapprovedRequest(
                    business_id: $data['business_id'],
                    purchase_id: $data['id'],
                    created_by: $data['user_id']
                ));
            }
        });
    }
}
