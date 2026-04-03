<?php

namespace Core\OrderItem\Application\DTOs;

class IndexOrderItemRequest
{
    public function __construct(
        public int $business_id,
        public int $created_by,
        public ?string $order_by = null,
        public ?string $keywords = null,
        public int $order_id,
        public int $paginate = 50
    ) {}

    /**
     * Create DTO from array input
     */
    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'],
            created_by: $data['user_id'],
            order_by: $data['order_by'] ?? 'DESC',
            keywords: $data['keywords'] ?? null,
            order_id: $data['order_id'],
            paginate: $data['paginate'] ?? 50
        );
    }

    /**
     * Convert DTO to array (Repository or Entity)
     */
    public function toArray(): array
    {
        return [
            'business_id' => $this->business_id,
            'created_by' => $this->created_by,
            'order_by' => $this->order_by,
            'keywords' => $this->keywords,
            'order_id' => $this->order_id,
            'paginate' => $this->paginate
        ];
    }
}
