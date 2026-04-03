<?php

namespace Core\Order\Application\DTOs;

class IndexOrderRequest
{
    public function __construct(
        public int $business_id,
        public ?string $keywords = null,
        public ?string $order_by = null,
        public int $created_by,
        public int $paginate = 15
    ) {}

    /**
     * Build DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'],
            created_by: $data['user_id'],
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC',
            paginate: $data['paginate'] ?? 15
        );
    }

    /**
     * Convert to array (for Entity or Repository)
     */
    public function toArray(): array
    {
        return [
            'business_id'            => $this->business_id,
            'created_by' => $this->created_by,
            'keywords' => $this->keywords,
            'order_by' => $this->order_by,
            'paginate' => $this->paginate
        ];
    }
}
