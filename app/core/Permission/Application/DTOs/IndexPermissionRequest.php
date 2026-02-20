<?php

namespace Core\Permission\Application\DTOs;

class IndexPermissionRequest
{
    public function __construct(
        public ?int $business_id,
        public ?int $user_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'],
            user_id: $data['user_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'business_id' => $this->business_id,
            'user_id' => $this->user_id,
        ];
    }
}
