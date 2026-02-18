<?php

namespace Core\Customer\Application\DTOs;

class IndexCustomerRequest
{
    public function __construct(
        public ?string $keywords,
        public ?int $business_id,
        public ?int $created_by,
        public ?string $order_by = null,
        public ?bool $active = null
    ) {}
    public static function fromArray(array $data): self
    {
        return new self(
            keywords: $data['keywords'] ?? null,
            business_id: $data['business_id'],
            created_by: $data['user_id'],
            order_by: $data['order_by'] ?? 'DESC',
            active: $data['active'] ?? null
        );
    }
    public function toArray(): array
    {
        return [
            'keywords'         => $this->keywords,
            'business_id'   => $this->business_id,
            'created_by'   => $this->created_by,
            'order_by'  => $this->order_by,
            'active'    => $this->active
        ];
    }
}
