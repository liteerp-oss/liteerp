<?php

namespace Core\CustomInvoiceIn\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\CustomInvoiceIn\Domain\Services\CustomInvoiceInService;
use Core\CustomInvoiceIn\Domain\Repositories\CustomInvoiceInRepositoryInterface;
use Core\CustomInvoiceIn\Domain\Entities\CustomInvoiceIn;

class CustomInvoiceInServiceImpl implements CustomInvoiceInService
{
    public function __construct(private CustomInvoiceInRepositoryInterface $repo) {}

    public function create(array $data): CustomInvoiceIn | BadException
    {
        if($this->repo->findByDocumentNo($data)) {
            throw new BadException(__("custominvoicein::messages.document_no_used"));
        }
        $entity = CustomInvoiceIn::fromArray($data);
        $entity->makeDocumentNo();
        return $this->repo->create($entity);
    }
    public function update(array $data): CustomInvoiceIn | BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("custominvoicein::messages.not_found"));
        }
        if($entity->document_no !== $data['document_no']) {
            if($this->repo->findByDocumentNo($data)) {
                throw new BadException(__("custominvoicein::messages.document_no_used"));
            }    
        }
        $entity->document_no = $data['document_no'] ?? $entity->document_no;
        $entity->makeDocumentNo();
        $entity->description = $data['description'] ?? $entity->description;
        $entity->amount = $data['amount'] ?? $entity->amount;
        $entity->invoice_date = $data['invoice_date'] ?? $entity->invoice_date;
        $entity->approved = $data['approved'] ?? $entity->approved;
        $entity->payment_status = $data['payment_status'] ?? $entity->payment_status;
        return $this->repo->update($entity);
    }
    public function delete(array $data): CustomInvoiceIn | BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("custominvoicein::messages.not_found"));
        }

        return $this->repo->delete($entity);
    }
}