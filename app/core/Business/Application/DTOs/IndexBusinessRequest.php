<?php

namespace Core\Business\Application\DTOs;

class IndexBusinessRequest
{
    public function __construct(
        public int $user_id  
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user_id: $data['user_id']
        );
    }
    public function toArray() : array{
        return [
            'user_id' => $this->user_id
        ];
    }
}