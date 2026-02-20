<?php

namespace Core\PermissionGroupUser\Domain\Entities;

class PermissionGroupUser
{
    public function __construct(
        public int $group_id,
        public int $account_id,
        public ?int $id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            group_id: (int) $data['group_id'],
            account_id: (int) $data['account_id'],
            id: isset($data['id']) ? (int) $data['id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'account_id' => $this->account_id,
        ];
    }
}
