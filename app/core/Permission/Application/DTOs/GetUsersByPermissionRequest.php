<?php

namespace Core\Permission\Application\DTOs;

class GetUsersByPermissionRequest
{
    public function __construct(
        public ?array $permissions = [],
        public int $business_id,
        public int $user_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            permissions: $data['permissions'],
            business_id: $data['business_id'],
            user_id: $data['user_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'permissions' => $this->permissions,
            'business_id' => $this->business_id,
            'user_id' => $this->user_id,
        ];
    }
}
