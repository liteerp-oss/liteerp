<?php

namespace Core\PermissionGroup\Domain\Entities;

class PermissionGroup
{
    public function __construct(
        public string $name,
        public ?string $type,
        public int $user_id,
        public int $business_id,
        public ?int $id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            type: $data['type'] ?? null,
            user_id: $data['user_id'],
            business_id: $data['business_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'user_id' => $this->user_id,
            'business_id' => $this->business_id,
        ];
    }
    public function setAdmin(): void
    {
        $this->type = 'admin';
    }
    public function setDefault(): void
    {
        $this->type = null;
    }
    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }
    public function isDefault(): bool
    {
        return $this->type === null;
    }
}
