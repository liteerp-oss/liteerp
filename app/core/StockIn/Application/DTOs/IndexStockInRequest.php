<?php

namespace Core\StockIn\Application\DTOs;

class IndexStockInRequest
{
    public function __construct(
        public int $business_id,
        public int $created_by,
        public ?string $keywords,
        public ?string $order_by = null 
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: (int) $data['business_id'],
            created_by: $data['user_id'],
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC' 
        );
    }

    public function toArray(): array
    {
        return [
            'business_id' => $this->business_id,
            'keywords' => $this->keywords,
            'created_by'  => $this->created_by,
            'order_by'  => $this->order_by
        ];
    }
}
