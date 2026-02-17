<?php

namespace Core\InventoryAdjustment\Application\DTOs;

class IndexInventoryAdjustmentRequest
{
    public function __construct(
        public ?int $business_id,
        public ?int $created_by,
        public ?string $keywords = null,
        public ?string $order_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'] ?? null,
            created_by: $data['user_id'] ?? null,
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC' 
        );
    }

    public function toArray(): array
    {
        return [
            'business_id'   => $this->business_id,
            'created_by'    => $this->created_by,
            'keywords'      => $this->keywords,
            'order_by'      => $this->order_by
        ];
    }
}