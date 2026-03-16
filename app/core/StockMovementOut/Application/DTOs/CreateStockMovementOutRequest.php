<?php

namespace Core\StockMovementOut\Application\DTOs;

class CreateStockMovementOutRequest
{
    public function __construct(
        public int $order_item_id,
        public int $created_by,
        public int $business_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            order_item_id: (int) $data['order_item_id'],
            created_by: (int) $data['user_id'],
            business_id : (int) $data['business_id']
        );
    }

    public function toArray(): array
    {
        return [
            'order_item_id'  => $this->order_item_id,
            'created_by'  => $this->created_by,
            'business_id' => $this->business_id
        ];
    }
}
