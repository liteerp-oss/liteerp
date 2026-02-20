<?php

namespace Core\Permission\Application\DTOs;

class CheckPermissionRequest
{
    public function __construct(
        public ?string $permission,
        public int $business_id,
        public int $user_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            permission: $data['permission'],
            business_id: $data['business_id'],
            user_id: $data['user_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'permission' => $this->permission,
            'business_id' => $this->business_id,
            'user_id' => $this->user_id,
        ];
    }
}
