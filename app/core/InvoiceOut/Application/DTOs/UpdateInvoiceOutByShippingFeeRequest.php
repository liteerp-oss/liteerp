<?php

namespace Core\InvoiceOut\Application\DTOs;

class UpdateInvoiceOutByShippingFeeRequest
{
    public function __construct(
        public int $business_id,
        public int $created_by,
        public float $shipping_fee_estimated,
        public float $shipping_fee_actual,
        public float $old_shipping_fee_actual,
        public int $order_id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: (int)$data['business_id'],
            shipping_fee_estimated: $data['shipping_fee_estimated'],
            shipping_fee_actual: $data['shipping_fee_actual'],
            order_id: $data['order_id'] ?? null,
            created_by: $data['user_id'] ?? null,
            old_shipping_fee_actual: $data['old_shipping_fee_actual']
        );
    }

    public function toArray(): array
    {
        return [
            'business_id'  => $this->business_id,
            'shipping_fee_estimated'        => $this->shipping_fee_estimated,
            'shipping_fee_actual'   => $this->shipping_fee_actual,
            'order_id' => $this->order_id,
            'created_by'   => $this->created_by,
            'old_shipping_fee_actual' => $this->old_shipping_fee_actual
        ];
    }
}
