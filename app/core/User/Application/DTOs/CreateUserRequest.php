<?php

namespace Core\User\Application\DTOs;

class CreateUserRequest
{
    public function __construct(
        public string $email,
        public int $created_by,
        public int $business_id,
        public int $group_id,
        public ?int $id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            created_by: $data['user_id'],
            business_id: $data['business_id'],
            group_id: $data['group_id'],
            id: $data['id'] ?? null 
        );
    }
    public function toArray()
    {
        return [
            'email' => $this->email,
            'created_by' => $this->created_by,
            'business_id'   => $this->business_id,
            'group_id'  => $this->group_id,
            'id'    => $this->id
        ];
    }
}
