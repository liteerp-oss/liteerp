<?php

namespace Core\StockMovementIn\Application\DTOs;

class CreateStockMovementInRequest
{
    public function __construct(
        public int $product_id,
        public int $warehouse_id,
        public int $qty_change,
        public int $stock_in_id,
        public int $purchase_item_id,
        public int $business_id,
        public int $created_by,
        public ?int $id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            warehouse_id: $data['warehouse_id'],
            qty_change: $data['qty_change'],
            stock_in_id: $data['stock_in_id'],
            purchase_item_id: $data['purchase_item_id'],
            business_id: $data['business_id'],
            created_by : $data['user_id'],
            id: $data['id'] ?? null
        );
    }
    public function toArray(){
        return [
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'qty_change' => $this->qty_change,
            'stock_in_id' => $this->stock_in_id,
            'purchase_item_id' => $this->purchase_item_id,
            'business_id'   => $this->business_id,
            'created_by'   => $this->created_by,
            'id' => $this->id
        ];
    }
}