<?php

namespace Core\PurchaseItem\Application\DTOs;

class CreatePurchaseItemRequest
{
    public function __construct(
        public int $purchase_id,
        public int $product_id,
        public float $discount = 0,
        public float $tax = 0,
        public ?string $product_link = null,
        public float $buy_quantity = 0,
        public float $gift_quantity = 0,
        public float $compensation_quantity = 0,
        public float $conversion_quantity = 0,
        public int $unit_cost = 0,
        // common 
        public int $business_id,
        public int $user_id,
        public ?int $id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            purchase_id: (int) $data['purchase_id'],
            product_id: (int) $data['product_id'],
            discount: (float) ($data['discount'] ?? 0),
            tax: (float) $data['tax'],
            product_link: $data['product_link'] ?? null,
            buy_quantity: (float) ($data['buy_quantity'] ?? 0),
            gift_quantity: (float) ($data['gift_quantity'] ?? 0),
            compensation_quantity: (float) ($data['compensation_quantity'] ?? 0),
            conversion_quantity: (float) ($data['conversion_quantity'] ?? 0),
            unit_cost: (int) ($data['unit_cost'] ?? 0),
            // 
            business_id: (int) $data['business_id'],
            user_id: (int) $data['user_id'],
            id: $data['id'] ?? null 
        );
    }

    public function toArray(): array
    {
        return [
            'purchase_id'            => $this->purchase_id,
            'product_id'             => $this->product_id,
            'discount'               => $this->discount,
            'tax'                    => $this->tax,
            'product_link'           => $this->product_link,
            'buy_quantity'           => $this->buy_quantity,
            'gift_quantity'          => $this->gift_quantity,
            'compensation_quantity'  => $this->compensation_quantity,
            'conversion_quantity'    => $this->conversion_quantity,
            'unit_cost'              => $this->unit_cost,
            // common 
            'business_id'            => $this->business_id,
            'user_id'                => $this->user_id,
            'id'    => $this->id
        ];
    }
}
