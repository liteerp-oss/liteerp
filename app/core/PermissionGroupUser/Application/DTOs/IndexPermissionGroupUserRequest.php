<?php

namespace Core\PermissionGroupUser\Application\DTOs;

class IndexPermissionGroupUserRequest
{
    public function __construct(
        public ?int $group_id = null,
        public ?int $user_id = null,
        public ?string $order_by = null,
        public ?int $created_by = null,
        public ?int $business_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            group_id: isset($data['group_id']) ? (int) $data['group_id'] : null,
            user_id: isset($data['user_id']) ? (int) $data['user_id'] : null,
            order_by: $data['order_by'] ?? 'DESC',
            created_by: $data['auth_user_id'] ?? ($data['user_auth_id'] ?? ($data['created_by'] ?? null)),
            business_id: $data['business_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'order_by' => $this->order_by,
            'created_by' => $this->created_by,
            'business_id' => $this->business_id,
        ];
    }
}
