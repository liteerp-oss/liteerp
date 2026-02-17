<?php

namespace Core\PurchaseItem\Application\DTOs;

class IndexPurchaseItemRequest
{
    public function __construct(
        public int $business_id,
        public int $created_by,
        public ?string $keywords = null,
        public ?string $order_by = null,
        public ?int $purchase_id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: (int) $data['business_id'],
            created_by: $data['user_id'],
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC',
            purchase_id: $data['purchase_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'business_id' => $this->business_id,
            'created_by'    => $this->created_by,
            'keywords'  => $this->keywords,
            'order_by'  => $this->order_by,
            'purchase_id' => $this->purchase_id
        ];
    }
}
