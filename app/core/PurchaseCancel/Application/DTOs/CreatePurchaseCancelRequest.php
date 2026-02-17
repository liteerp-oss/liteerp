<?php

namespace Core\PurchaseCancel\Application\DTOs;

class CreatePurchaseCancelRequest
{
    public function __construct(
        public int $purchase_id,
        public ?string $reason = null,
        public int $created_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            purchase_id: $data['id'],
            reason: $data['reason'] ?? null,
            created_by: $data['user_id'] 
        );
    }
    public function toArray(): array
    {
        return [
            'purchase_id' => $this->purchase_id,
            'reason'    => $this->reason,
            'created_by' => $this->created_by
        ];
    }
}