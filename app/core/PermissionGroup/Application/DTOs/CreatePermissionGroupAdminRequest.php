<?php

namespace Core\PermissionGroup\Application\DTOs;

class CreatePermissionGroupAdminRequest
{
    public function __construct(
        public ?int $created_by = null,
        public ?int $business_id = null,
        public ?string $name = 'Admin'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            created_by: $data['user_id'] ?? null,
            business_id: $data['business_id'] ?? null,
            name: 'Admin'
        );
    }

    public function toArray(): array
    {
        return [
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
            'name' => $this->name,
        ];
    }
}
