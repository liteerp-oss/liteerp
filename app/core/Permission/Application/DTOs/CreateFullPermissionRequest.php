<?php

namespace Core\Permission\Application\DTOs;

class CreateFullPermissionRequest
{
    public function __construct(
        public ?int $group_id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            group_id: $data['group_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->group_id
        ];
    }
}
