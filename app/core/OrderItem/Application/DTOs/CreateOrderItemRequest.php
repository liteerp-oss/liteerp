<?php

namespace Core\OrderItem\Application\DTOs;

class CreateOrderItemRequest
{
    public function __construct(
        public ?int $business_id = null,
        public ?int $created_by = null,
        public int $order_id,
        public int $stock_movements_in_id,
        public float $discount = 0,
        public float $tax = 0,
        public float $buy_quantity = 0,
        public float $gift_quantity = 0,
        public float $compensation_quantity = 0,
        public float $conversion_quantity = 0,
        public float $price,
        public ?int $id = null 
    ) {}

    /**
     * Create DTO from array input
     */
    public static function fromArray(array $data): self
    {
        return new self(
            order_id:               $data['order_id'],
            stock_movements_in_id:  $data['stock_movements_in_id'],
            discount:               (float)($data['discount'] ?? 0),
            tax:                    (float) $data['tax'],
            buy_quantity:           (float)($data['buy_quantity'] ?? 0),
            gift_quantity:          (float)($data['gift_quantity'] ?? 0),
            compensation_quantity:  (float)($data['compensation_quantity'] ?? 0),
            conversion_quantity:    (float)($data['conversion_quantity'] ?? 0),
            business_id: (int) $data['business_id'] ?? null,
            created_by: (int) $data['user_id'] ?? null,
            price: (float) $data['price'],
            id: $data['id'] ?? null 
        );
    }

    /**
     * Convert DTO to array (Repository or Entity)
     */
    public function toArray(): array
    {
        return [
            'order_id'              => $this->order_id,
            'stock_movements_in_id' => $this->stock_movements_in_id,
            'discount'              => $this->discount,
            'tax'                   => $this->tax,
            'buy_quantity'          => $this->buy_quantity,
            'gift_quantity'         => $this->gift_quantity,
            'compensation_quantity' => $this->compensation_quantity,
            'conversion_quantity'   => $this->conversion_quantity,
            'business_id' => $this->business_id,
            'created_by' => $this->created_by,
            'price'   => $this->price,
            'id'      => $this->id
        ];
    }
    public function totalQuantity(){
        return $this->buy_quantity
        + $this->gift_quantity
        + $this->compensation_quantity
        + $this->conversion_quantity;
    }
}
