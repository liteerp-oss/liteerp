<?php

namespace Core\Inventory\Application\DTOs;

class IndexInventoryRequest
{
    public function __construct(
        public ?string $keywords = null,
        public ?string $order_by = null,
        public int $business_id,
        public int $created_by,
        // search for select to take order 
        public ?int $order_id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC',
            business_id: $data['business_id'],
            created_by: $data['user_id'],
            order_id: $data['order_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'keywords' => $this->keywords,
            'order_by' => $this->order_by,
            'business_id' => $this->business_id,
            'created_by' => $this->created_by,
            'order_id'  => $this->order_id
        ];
    }
}
