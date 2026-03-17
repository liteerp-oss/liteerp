<?php

namespace Core\Purchase\Application\DTOs;

class IndexPurchaseRequest
{
    
    public function __construct(
        public ?string $keywords = null,
        public ?string $status = null,
        public ?string $order_by = null,
        public int $created_by,
        public int $business_id,
        public ?bool $isCompleted = null 
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keywords: $data['keywords'] ?? null,
            status: $data['status'] ?? null,
            order_by: $data['order_by'] ?? 'DESC',
            created_by: $data['user_id'],
            business_id: $data['business_id'],
            isCompleted: $data['isCompleted'] ?? null 
        );
    }

    public function toArray(): array
    {
        return [
            'keywords'   => $this->keywords,
            'status'   => $this->status,
            'created_by'    => $this->created_by,
            'business_id'    => $this->business_id,
            'order_by'  => $this->order_by,
            'isCompleted' => $this->isCompleted
        ];
    }
}
