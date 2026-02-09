<?php

namespace Core\User\Domain\Entities;

use Carbon\Carbon;

class User
{
    public function __construct(
        public ?int $id = null,
        public string $email,
        public ?string $role = null,
        public ?int $business_id,
        public ?string $lang = null,
        public ?string $avatar = null,
    ) {}

    /**
     * Factory: Create Entity from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            email: $data['email'],
            role: $data['role'] ?? null,
            business_id : $data['business_id'] ?? null,
            lang: $data['lang'] ?? null,
            avatar: $data['avatar'] ?? null 
        );
    }

    /**
     * Convert Entity → array (for repository)
     */
    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'email'             => $this->email,
            'role'              => $this->role,
            'business_id'       => $this->business_id,
            'lang'              => $this->lang,  
            'avatar'            => $this->avatar
        ];
    }
}
