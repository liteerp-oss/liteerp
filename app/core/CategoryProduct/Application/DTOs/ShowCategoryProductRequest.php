<?php

namespace Core\CategoryProduct\Application\DTOs;

class ShowCategoryProductRequest
{
    public function __construct(
        public int $created_by,
        public int $business_id,
        public int $id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'],
            created_by: $data['user_id'],
            id: $data['id']
        );
    }
    public function toArray() : array {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'created_by' => $this->created_by
        ];
    }
}