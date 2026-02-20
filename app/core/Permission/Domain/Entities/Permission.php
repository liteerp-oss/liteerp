<?php

namespace Core\Permission\Domain\Entities;

class Permission
{
    public function __construct(
        public string $permission,
        public int $group_id,
        public ?int $id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            permission: $data['permission'],
            group_id: $data['group_id'] ?? null,
            id: $data['id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'permission' => $this->permission,
            'group_id' => $this->group_id
        ];
    }
}
