<?php

namespace Core\PermissionGroupUser\Application\DTOs;

class ShowPermissionGroupUserRequest
{
    public function __construct(
        public ?int $id = null,
        public ?int $created_by = null,
        public ?int $business_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            created_by: $data['auth_user_id'] ?? ($data['user_auth_id'] ?? ($data['created_by'] ?? null)),
            business_id: $data['business_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
        ];
    }
}
