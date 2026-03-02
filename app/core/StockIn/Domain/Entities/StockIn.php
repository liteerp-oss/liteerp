<?php

namespace Core\StockIn\Domain\Entities;

class StockIn
{
    public ?int $id = null;

    public function __construct(
        public int $business_id,
        public int $invoice_in_id,
        public ?int $approved_by = null,
        public ?string $import_date = null,
        public ?string $status = null,
    ) {}
    public static function fromArray(array $data): self
    {
        $entity = new self(
            business_id: (int) $data['business_id'],
            invoice_in_id: (int) $data['invoice_in_id'],
            approved_by: $data['approved_by'] ?? null,
            import_date: $data['import_date'] ?? null,
            status: $data['status'] ?? null 
        );

        if (isset($data['id'])) {
            $entity->id = (int) $data['id'];
        }

        return $entity;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'invoice_in_id' => $this->invoice_in_id,
            'approved_by' => $this->approved_by,
            'import_date' => $this->import_date,
            'status'    => $this->status
        ];
    }
    public function setImportDate(){
        $this->import_date = date('Y-m-d',time());
    }
    public function markPending(){
        $this->status = 'pending';
    }
    public function markReceived(){
        $this->status = 'received';
    }
    public function markCancelled(){
        $this->status = 'cancelled';
    }
    public function isPending(): bool{
        return $this->status === 'pending' ? true : false;
    }
    public function isReceived(): bool{
        return $this->status === 'received' ? true : false;
    }
    public function getCurrentStatus() : string{
        return $this->status;
    }
    public function isCancelled(): bool{
        return $this->status === 'cancelled' ? true : false;
    }
    public function getStatus() : string {
        return $this->status;
    }
}
