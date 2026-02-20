<?php

namespace Core\User\Application\DTOs;

class DeleteUserRequest
{
    public function __construct(
        public ?int $created_by = null,
        public ?int $business_id = null,
        public int $id,
        public int $group_id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            created_by: $data['user_id'],
            business_id: $data['business_id'],
            id: $data['id'],
            group_id: $data['group_id'] 
        );
    }
    public function toArray() : array
    {
        return [
            'created_by' => $this->created_by,
            'business_id'   => $this->business_id,
            'id'    => $this->id,
            'group_id' => $this->group_id
        ];
    }
}
