<?php

namespace Core\PermissionGroup\Application\DTOs;

class IndexPermissionGroupRequest
{
    public function __construct(
        public ?string $keywords = null,
        public ?string $order_by = null,
        public ?int $created_by = null,
        public ?int $business_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC',
            created_by: $data['user_id'] ?? null,
            business_id: $data['business_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'keywords' => $this->keywords,
            'order_by' => $this->order_by,
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
        ];
    }
}
