<?php

namespace Core\CustomInvoiceOut\Application\DTOs;

class CreateCustomInvoiceOutRequest
{
    public function __construct(
        public int $created_by,
        public int $business_id,
        public int $customer_id,
        public string $description,
        public float $amount,
        public ?string $invoice_date,
        public ?bool $approved = null,
        public string $payment_status = 'pending',
        public ?string $document_no = null,
        public ?int $id = null 
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            created_by: (int) $data['user_id'],
            business_id: (int) $data['business_id'],
            customer_id: (int) $data['customer_id'],
            description: $data['description'],
            amount: (float) $data['amount'],
            invoice_date: $data['invoice_date'],
            approved: $data['approved'] ?? null,
            payment_status: $data['payment_status'] ?? 'pending',
            document_no: $data['document_no'] ?? null,
            id: $data['id'] ?? null  
        );
    }

    public function toArray(): array
    {
        return [
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
            'description' => $this->description,
            'amount' => $this->amount,
            'invoice_date' => $this->invoice_date,
            'approved' => $this->approved,
            'payment_status' => $this->payment_status,
            'document_no'   => $this->document_no,
            'id'    => $this->id,
            'customer_id'   => $this->customer_id
        ];
    }
}
