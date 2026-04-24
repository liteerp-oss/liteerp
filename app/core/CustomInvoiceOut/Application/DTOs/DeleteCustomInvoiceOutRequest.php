<?php

namespace Core\CustomInvoiceOut\Application\DTOs;

class DeleteCustomInvoiceOutRequest
{
    public function __construct(
        public int $created_by,
        public int $business_id,
        public ?int $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            created_by: (int) $data['user_id'],
            business_id: (int) $data['business_id'],
            id: $data['id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
            'id' => $this->id
        ];
    }
}
