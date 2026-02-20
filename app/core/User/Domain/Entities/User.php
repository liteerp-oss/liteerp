<?php

namespace Core\User\Domain\Entities;

use Carbon\Carbon;

class User
{
    public function __construct(
        public ?int $id = null,
        public string $email,
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
            'lang'              => $this->lang,  
            'avatar'            => $this->avatar
        ];
    }
}
