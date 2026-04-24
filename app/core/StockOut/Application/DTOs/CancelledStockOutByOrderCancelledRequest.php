<?php

namespace Core\StockOut\Application\DTOs;

class CancelledStockOutByOrderCancelledRequest
{
    public function __construct(
        public int $business_id,
        public int $invoice_out_id,
        public ?int $created_by = null,
        public int $order_id,
        public ?string $username = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'],
            invoice_out_id: $data['invoice_out_id'],
            created_by: $data['user_id'] ?? null,
            order_id: $data['order_id'],
            username: $data['username'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'business_id'    => $this->business_id,
            'invoice_out_id' => $this->invoice_out_id,
            'created_by'     => $this->created_by,
            'order_id'  => $this->order_id,
            'username'  => $this->username
        ];
    }
}
