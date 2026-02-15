<?php

namespace Core\CategoryProduct\Application\DTOs;

class CreateCategoryProductRequest
{
    public function __construct(
        public string $name,
        public int $business_id,
        public ?string $description = null,
        public int $created_by,
        public ?int $id,
        public int $tax,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            business_id: $data['business_id'],
            description: $data['description'] ?? null,
            created_by: $data['user_id'] ?? null,
            id: $data['id'] ?? null,
            tax: $data['tax'],
        );
    }
    public function toArray() : array {
        return [
            'name' => $this->name,
            'business_id' => $this->business_id,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'id' => $this->id,
            'tax' => $this->tax,
        ];
    }
}