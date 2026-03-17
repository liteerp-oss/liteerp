<?php

namespace Core\InventoryAdjustment\Application\DTOs;

use App\Exceptions\BadException;

class CreateInventoryAdjustmentRequest
{
    public function __construct(
        public int $stock_movements_in_id,
        public float $qty_adjusted,
        public ?string $reason,
        public int $adjusted_by,
        public ?int $id = null,
        public ?int $business_id,
        public ?int $created_by,
        public ?int $purchase_id
    ) {
        if($this->qty_adjusted >= 0) {
            throw new BadException(__("inventoryadjustment::messages.adjusted_by_invalid"));
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            stock_movements_in_id: $data['stock_movements_in_id'],
            qty_adjusted: (float) $data['qty_adjusted'],
            reason: $data['reason'] ?? null,
            adjusted_by: $data['user_id'],
            id: $data['id'] ?? null,
            business_id: $data['business_id'] ?? null,
            created_by: $data['user_id'] ?? null,
            purchase_id: $data['purchase_id']
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'stock_movements_in_id'    => $this->stock_movements_in_id,
            'qty_adjusted'  => $this->qty_adjusted,
            'reason'        => $this->reason,
            'adjusted_by'   => $this->adjusted_by,
            'business_id'   => $this->business_id,
            'created_by'    => $this->created_by,
            'purchase_id'   => $this->purchase_id
        ];
    }
}