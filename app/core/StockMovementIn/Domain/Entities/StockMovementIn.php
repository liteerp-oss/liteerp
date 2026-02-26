<?php

namespace Core\StockMovementIn\Domain\Entities;

class StockMovementIn
{
    public function __construct(
        public int $product_id,
        public int $warehouse_id,
        public int $qty_change,
        public int $stock_in_id,
        public ?int $id = null,
        public int $created_by
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            warehouse_id: $data['warehouse_id'],
            qty_change: $data['qty_change'],
            stock_in_id: $data['stock_in_id'],
            id: $data['id'] ?? null,
            created_by: $data['created_by'] 
        );
    }
    public function toArray()
    {
        return [
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'qty_change' => $this->qty_change,
            'stock_in_id' => $this->stock_in_id,
            'id' => $this->id,
            'created_by' => $this->created_by 
        ];
    }
}
