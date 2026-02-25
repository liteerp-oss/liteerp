<?php

namespace Core\InvoiceOut\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\InvoiceOut\Domain\Services\InvoiceOutService;
use Core\InvoiceOut\Domain\Repositories\InvoiceOutRepositoryInterface;
use Core\InvoiceOut\Domain\Entities\InvoiceOut;

class InvoiceOutServiceImpl implements InvoiceOutService
{
    public function __construct(private InvoiceOutRepositoryInterface $repo) {}

    public function create(array $data): InvoiceOut
    {
        $entity = InvoiceOut::fromArray($data);
        $entity->setDocumentNo();
        $entity->setDueDate();
        $entity->setInvoiceDate();
        return $this->repo->create($entity);
    }
    public function show(array $data): array|BadException
    {
        $row = $this->repo->findWithFullData($data);
        if (!$row) {
            throw new BadException(__("invoiceout::messages.not_found"));
        }
        return $row;
    }
    public function update(array $data): InvoiceOut|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("invoiceout::messages.not_found"));
        }
        if ($entity->isApproved() && $entity->isApproved() !== $data['approved']) {
            throw new BadException(__("invoiceout::messages.approved_cannot_change"));
        }
        $entity->document_no = $data['document_no'] ?? $entity->document_no;
        $entity->invoice_date = $data['invoice_date'] ?? $entity->invoice_date;
        $entity->due_date = $data['due_date'] ?? $entity->due_date;
        $entity->approved = $data['approved'] ?? $entity->approved;
        $entity->payment_status = $data['payment_status'] ?? $entity->payment_status;
        $entity->image = $data['image'] ?? $entity->image;
        $entity->amount_paid = $data['amount_paid'] ?? $entity->amount_paid;
        $entity->total = $data['total'] ?? $entity->total;
        if (!$entity->checkAmountPaidValid()) {
            throw new BadException(__("invoiceout::messages.partial_payment"));
        }
        if ($entity->isPaid()) {
            // paid full money 
            $entity->markAmountPaid();
        }
        return $this->repo->update($entity);
    }
    public function unApproved(array $data): InvoiceOut|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("invoiceout::messages.not_found"));
        }
        $entity->markUnApproved();
        return $this->repo->update($entity);
    }
    public function findById(array $data): InvoiceOut|BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("invoiceout::messages.not_found"));
    }
    public function getByOrderId(array $data): ?InvoiceOut
    {
        return $this->repo->findByOrderId($data);
    }
    public function findByOrderId(array $data): InvoiceOut|BadException
    {
        return $this->repo->findByOrderId($data) ?? throw new BadException(__("invoiceout::messages.not_found"));
    }
    public function changeShippingFee(array $data): InvoiceOut|BadException
    {
        $entity = $this->repo->findByOrderId($data);
        if(!$entity) {
            throw new BadException(__("invoiceout::messages.not_found"));
        }
        $entity->total = $entity->total - $data['shipping_fee_estimated'] 
            + ($data['shipping_fee_actual'] - $data['old_shipping_fee_actual']); 
        return $this->repo->update($entity);
    }
}
