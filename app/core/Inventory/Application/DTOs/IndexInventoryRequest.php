<?php

namespace Core\Inventory\Application\DTOs;

class IndexInventoryRequest
{
    public function __construct(
        public ?string $keywords = null,
        public ?string $order_by = null,
        public int $business_id,
        public int $created_by,
        public ?int $paginate
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC',
            business_id: $data['business_id'],
            created_by: $data['user_id'],
            paginate: $data['paginate'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'keywords' => $this->keywords,
            'order_by' => $this->order_by,
            'business_id' => $this->business_id,
            'created_by' => $this->created_by,
            'paginate' => $this->paginate
        ];
    }
}
