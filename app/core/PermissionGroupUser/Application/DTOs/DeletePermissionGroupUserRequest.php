<?php

namespace Core\PermissionGroupUser\Application\DTOs;

class DeletePermissionGroupUserRequest
{
    public function __construct(
        public int $group_id,
        public int $created_by,
        public int $business_id,
        public int $user_id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            group_id: $data['group_id'],
            created_by: $data['created_by'],
            business_id: $data['business_id'],
            user_id: $data['account_id']
        );
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->group_id,
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
            'user_id'   => $this->user_id
        ];
    }
}
