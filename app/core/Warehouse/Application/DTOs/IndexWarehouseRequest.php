<?php

namespace Core\Warehouse\Application\DTOs;

class IndexWarehouseRequest
{
    public function __construct(
        public ?string $keywords = null,
        public int $created_by,
        public int $business_id,
        public ?string $order_by = null,
        public ?bool $active = null
    ) {}
    public static function fromArray(array $data) : self {
        return new self(
            keywords: $data['keywords'] ?? null,
            created_by: $data['user_id'],
            business_id: $data['business_id'],
            order_by: $data['order_by'] ?? 'DESC',
            active: $data['active'] ?? null 
        );
    }
    public function toArray(): array {
        return [
            'keywords' => $this->keywords,
            'created_by' => $this->created_by,
            'business_id'   => $this->business_id,
            'order_by'  => $this->order_by,
            'active' => $this->active
        ];
    }
}
