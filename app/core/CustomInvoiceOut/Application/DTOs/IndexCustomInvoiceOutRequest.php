<?php

namespace Core\CustomInvoiceOut\Application\DTOs;

class IndexCustomInvoiceOutRequest
{
    public function __construct(
        public int $createdBy,
        public int $business_id,
        public ?bool $approved = null,
        public ?string $keywords = null,
        public ?string $order_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            createdBy: (int) $data['user_id'],
            business_id: (int) $data['business_id'],
            approved: $data['approved'] ?? null,
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC'
        );
    }

    public function toArray(): array
    {
        return [
            'created_by' => $this->createdBy,
            'business_id' => $this->business_id,
            'approved' => $this->approved,
            'keywords' => $this->keywords,
            'order_by' => $this->order_by
        ];
    }
}
