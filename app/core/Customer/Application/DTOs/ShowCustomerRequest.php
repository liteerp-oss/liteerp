<?php

namespace Core\Customer\Application\DTOs;

class ShowCustomerRequest
{
    public function __construct(
        public int $id,
        public string $created_by,
        public string $business_id
    ) {}
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            created_by: $data['user_id'],
            business_id: $data['business_id']
        );
    }
    public function toArray(): array
    {
        return [
            'id'  => $this->id,
            'created_by'         => $this->created_by,
            'business_id'   => $this->business_id
        ];
    }
}
