<?php

namespace Core\Shipping\Application\DTOs;

class IndexShippingRequest
{
    public function __construct(
        public ?bool $active = null,
        public ?string $keywords = null,
        public int $business_id,
        public int $created_by,
        public ?string $order_by = null  
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            active: $data['active'] ?? null,
            business_id: $data['business_id'],
            keywords: $data['keywords'] ?? null,
            created_by: $data['user_id'],
            order_by: $data['order_by'] ?? 'DESC'
        );
    }

    public function toArray(): array
    {
        return [
            'active' => $this->active,
            'business_id' => $this->business_id,
            'keywords'     => $this->keywords,
            'created_by'    => $this->created_by,
            'order_by'      => $this->order_by
        ];
    }
}