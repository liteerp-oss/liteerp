<?php

namespace Core\StockMovementOut\Domain\Entities;

class StockMovementOut
{

    public function __construct(
        public int $order_item_id,
        public int $created_by,
        public ?int $id = null
    ) {
        
    }

    public static function fromArray(array $data): self
    {
        return new self(
            order_item_id: (int) $data['order_item_id'],
            created_by: (int) $data['created_by'],
            id: $data['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'order_item_id'   => $this->order_item_id,
            'created_by'   => $this->created_by
        ];
    }
}
