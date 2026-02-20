<?php

namespace Core\PermissionGroup\Application\DTOs;

class CreatePermissionGroupRequest
{
    public function __construct(
        public string $name,
        public ?string $type,
        public ?int $id = null,
        public ?int $created_by = null,
        public ?int $business_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'] ?? null,
            id: $data['id'] ?? null,
            created_by: $data['user_id'] ?? null,
            business_id: $data['business_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'id' => $this->id,
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
        ];
    }
}
