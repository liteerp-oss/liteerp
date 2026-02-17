<?php

namespace Core\Inventory\Application\DTOs;

class IndexInventoryRequest
{
    public function __construct(
        public ?string $keywords = null,
        public ?string $order_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC'
        );
    }

    public function toArray(): array
    {
        return [
            'keywords' => $this->keywords,
            'order_by' => $this->order_by
        ];
    }
}
