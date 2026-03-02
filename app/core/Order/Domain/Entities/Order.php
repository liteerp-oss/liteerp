<?php

namespace Core\Order\Domain\Entities;

use Carbon\Carbon;
use Illuminate\Support\Str;
class Order
{
    public ?int $id = null;

    public function __construct(
        public int $business_id,
        public ?string $order_no = null,
        public ?int $customer_id = null,
        public ?string $order_date = null,
        public ?string $expected_delivery_date = null,
        public string $status = 'pending',    
        public ?int $created_by = null,
        public ?int $approved_by = null,
        public ?int $updated_by = null,
        public ?string $note = null,
        public string $type = 'retail'      
        
    ) {}

    /**
     * Build entity from array
     */
    public static function fromArray(array $data): self
    {
        $entity = new self(
            business_id:             $data['business_id'],
            order_no:                $data['order_no'] ?? null,
            customer_id:             $data['customer_id'] ?? null,
            order_date:              $data['order_date'] ?? null,
            expected_delivery_date:  $data['expected_delivery_date'] ?? null,
            status:                  $data['status'] ?? 'pending',
            created_by:              $data['created_by'] ?? null,
            approved_by:             $data['approved_by'] ?? null,
            updated_by:              $data['updated_by'] ?? null,
            note:                    $data['note'] ?? null,
            type:                    $data['type'] ?? 'retail'
        );

        $entity->id = $data['id'] ?? null;

        return $entity;
    }

    /**
     * Convert entity to array for repository
     */
    public function toArray(): array
    {
        return [
            'id'                      => $this->id,
            'business_id'             => $this->business_id,
            'order_no'                => $this->order_no,
            'customer_id'             => $this->customer_id,
            'order_date'              => Carbon::parse($this->order_date)->format('Y-m-d'),
            'expected_delivery_date'  => Carbon::parse($this->expected_delivery_date)->format('Y-m-d'),
            'status'                  => $this->status,
            'created_by'              => $this->created_by,
            'approved_by'             => $this->approved_by,
            'updated_by'             => $this->updated_by,
            'note'                    => $this->note,
            'type'                    => $this->type,
        ];
    }
    public function markPending(): void
    {
        $this->status = 'pending';
    } 
    public function isPending() : bool{
        return $this->status === 'pending' ? true : false;
    }
    public function markApprove(): void
    {
        $this->approved_by = $this->created_by;
        $this->status = 'approved';
    }
    public function isApproved() : bool {
        return $this->status === 'approved' ? true : false;
    }
    public function markCancelled(): void
    {
        $this->status = 'cancelled';
    }
    public function isCancelled(){
        return $this->status === 'cancelled' ? true : false;
    }
    public function makeOrderNo(){
        $this->order_no = "SO-" . $this->customer_id . "-" . date('YmdHis',time());
    }
    public function makeOrderNoIfNull(){
        $this->order_no ??= "SO-" . $this->customer_id . "-" . date('YmdHis',time());
    }
    public function makeOrderDate(){
        $this->order_date = date('Y-m-d',time());
    }
    public function getStatus() : string {
        return $this->status;
    }
}
