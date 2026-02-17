<?php

namespace Core\User\Application\DTOs;

class IndexUserRequest
{
    public function __construct(
        public int $created_by,
        public int $business_id,
        public ?string $keywords = null,
        public ?string $order_by = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            created_by: $data['user_id'],
            business_id: $data['business_id'],
            keywords: $data['keywords'] ?? null,
            order_by: $data['order_by'] ?? 'DESC'
        );
    }
    public function toArray()
    {
        return [
            'created_by' => $this->created_by,
            'business_id'   => $this->business_id,
            'keywords'  => $this->keywords,
            'order_by'  => $this->order_by
        ];
    }
}
