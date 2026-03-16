<?php

namespace Core\InventoryAdjustment\Domain\Entities;

class InventoryAdjustment
{
    public function __construct(
        public int $product_id,
        public int $warehouse_id,
        public float $qty_adjusted,
        public ?string $reason,
        public int $adjusted_by,
        public ?int $id = null,
        public int $purchase_id
    ) {}

    /**
     * Convert array -> Entity
     */
    public static function fromArray(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            warehouse_id: $data['warehouse_id'],
            qty_adjusted: (float) $data['qty_adjusted'],
            reason: $data['reason'] ?? null,
            adjusted_by: $data['adjusted_by'],
            id: $data['id'] ?? null,
            purchase_id: $data['purchase_id']
        );
    }

    /**
     * Convert Entity -> array
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'product_id'    => $this->product_id,
            'warehouse_id'  => $this->warehouse_id,
            'qty_adjusted'  => $this->qty_adjusted,
            'reason'        => $this->reason,
            'adjusted_by'   => $this->adjusted_by,
            'purchase_id'   => $this->purchase_id
        ];
    }
}
