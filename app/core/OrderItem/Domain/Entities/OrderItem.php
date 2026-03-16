<?php

namespace Core\OrderItem\Domain\Entities;

class OrderItem
{
    public ?int $id = null;

    public function __construct(
        public int $order_id,
        public int $stock_movements_in_id,
        public float $discount = 0,
        public float $buy_quantity = 0,
        public float $gift_quantity = 0,
        public float $compensation_quantity = 0,
        public float $conversion_quantity = 0,
        public float $tax = 0,
        public float $price = 0,
        public ?int $cancelled = 0
    ) {}
    public static function fromArray(array $data): self
    {
        $entity = new self(
            order_id:               $data['order_id'],
            stock_movements_in_id:             $data['stock_movements_in_id'],
            discount:               (float)($data['discount'] ?? 0),
            buy_quantity:           (float)($data['buy_quantity'] ?? 0),
            gift_quantity:          (float)($data['gift_quantity'] ?? 0),
            compensation_quantity:  (float)($data['compensation_quantity'] ?? 0),
            conversion_quantity:    (float)($data['conversion_quantity'] ?? 0),
            tax: (float) $data['tax'],
            price: (float) $data['price'],
            cancelled: $data['cancelled'] ?? 0
        );

        $entity->id = $data['id'] ?? null;

        return $entity;
    }
    public function toArray(): array
    {
        return [
            'id'                     => $this->id,
            'order_id'               => $this->order_id,
            'stock_movements_in_id'             => $this->stock_movements_in_id,
            'discount'               => $this->discount,
            'buy_quantity'           => $this->buy_quantity,
            'gift_quantity'          => $this->gift_quantity,
            'compensation_quantity'  => $this->compensation_quantity,
            'conversion_quantity'    => $this->conversion_quantity,
            'tax' => $this->tax,
            'price' => $this->price,
            'cancelled' => $this->cancelled
        ];
    }
    public function totalQuantity(): float
    {
        return
            $this->buy_quantity +
            $this->gift_quantity +
            $this->compensation_quantity +
            $this->conversion_quantity;
    }
}
