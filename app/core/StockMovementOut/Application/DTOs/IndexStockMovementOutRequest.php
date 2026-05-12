<?php

namespace Core\StockMovementOut\Application\DTOs;

class IndexStockMovementOutRequest
{
    public function __construct(
        public int $created_by,
        public int $business_id,
        public int $order_item_id,
        public ?string $keywords = null,
        public ?string $order_by = null,
        public ?int $stock_out_id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            created_by: (int) $data['user_id'],
            business_id : (int) $data['business_id'],
            order_item_id: (int) $data['order_item_id'],
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC',
            stock_out_id: $data['stock_out_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'order_item_id'=> $this->order_item_id,
            'created_by'  => $this->created_by,
            'business_id' => $this->business_id,
            'keywords'    => $this->keywords,
            'order_by'    => $this->order_by,
            'stock_out_id' => $this->stock_out_id
        ];
    }
}
