<?php

namespace Core\StockMovementIn\Application\DTOs;

class IndexStockMovementInRequest
{
    public function __construct(
        public int $business_id,
        public int $created_by,
        public int $stock_in_id,
        public ?string $keywords = null,
        public ?string $order_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'],
            created_by : $data['user_id'],
            stock_in_id: $data['stock_in_id'],
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC'
        );
    }
    public function toArray(){
        return [
            'business_id'   => $this->business_id,
            'created_by'   => $this->created_by,
            'stock_in_id' => $this->stock_in_id,
            'keywords' => $this->keywords,
            'order_by' => $this->order_by
        ];
    }
}