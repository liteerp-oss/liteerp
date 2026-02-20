<?php

namespace Core\PermissionGroupUser\Application\DTOs;

class CreatePermissionGroupUserRequest
{
    public function __construct(
        public int $group_id,
        public int $account_id,
        public ?int $id = null,
        public int $created_by,
        public int $business_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            group_id: (int) $data['group_id'],
            account_id: (int) $data['account_id'],
            id: isset($data['id']) ? (int) $data['id'] : null,
            created_by: $data['user_id'],
            business_id: $data['business_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->group_id,
            'account_id' => $this->account_id,
            'id' => $this->id,
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
        ];
    }
}
