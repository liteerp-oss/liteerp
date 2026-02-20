<?php

namespace Core\Permission\Application\DTOs;

class ShowPermissionRequest
{
    public function __construct(
        public ?int $group_id = null,
        public ?int $business_id,
        public ?int $user_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            group_id: $data['group_id'],
            business_id: $data['business_id'],
            user_id: $data['user_id']
        );
    }

    public function toArray(): array
    {
        return [
            'group_id' => $this->group_id,
            'business_id' => $this->business_id,
            'user_id' => $this->user_id
        ];
    }
}
