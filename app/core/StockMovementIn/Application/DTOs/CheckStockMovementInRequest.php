<?php

namespace Core\StockMovementIn\Application\DTOs;

class CheckStockMovementInRequest
{
    public function __construct(
        public int $business_id,
        public int $created_by,
        public int $id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            business_id: $data['business_id'],
            created_by : $data['user_id'],
            id: $data['stock_movements_in_id']
        );
    }
    public function toArray(){
        return [
            'business_id'   => $this->business_id,
            'created_by'   => $this->created_by,
            'id' => $this->id
        ];
    }
}