<?php

namespace Core\Customer\Application\DTOs;

class CreateCustomerRequest
{
    public function __construct(
        public int $business_id,
        public string $name,
        public ?string $contact_name = null,
        public ?string $email = null,
        public string $phone,
        public ?string $address = null,
        public ?string $tax_code = null,
        public ?string $national_id = null,
        public ?string $bank_name = null,
        public ?string $bank_account = null,
        public string $type = 'individual',      
        public int $group,
        public ?string $website = null,
        public ?string $note = null,
        public bool $active = true,
        public ?int $id = null,
        public ?int $created_by 
    ) {
        $this->formatPhoneNumber();
    }

    /**
     * Create DTO from array input
     */
    public static function fromArray(array $data): self
    {
        return new self(
            business_id:  $data['business_id'],
            name:         $data['name'],
            contact_name: $data['contact_name'] ?? null,
            email:        $data['email'] ?? null,
            phone:        $data['phone'],
            address:      $data['address'] ?? null,
            tax_code:     $data['tax_code'] ?? null,
            national_id:  $data['national_id'] ?? null,
            bank_name:    $data['bank_name'] ?? null,
            bank_account: $data['bank_account'] ?? null,
            type:         $data['type'] ?? 'individual',
            group:        $data['group'],
            website:      $data['website'] ?? null,
            note:         $data['note'] ?? null,
            active:       $data['active'] ?? true,
            id:           $data['id'] ?? null,
            created_by: $data['user_id'] ?? null
        );
    }

    /**
     * Convert DTO to array (for Repository or Entity)
     */
    public function toArray(): array
    {
        return [
            'business_id'  => $this->business_id,
            'name'         => $this->name,
            'contact_name' => $this->contact_name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'address'      => $this->address,
            'tax_code'     => $this->tax_code,
            'national_id'  => $this->national_id,
            'bank_name'    => $this->bank_name,
            'bank_account' => $this->bank_account,
            'type'         => $this->type,
            'group'        => $this->group,
            'website'      => $this->website,
            'note'         => $this->note,
            'active'       => $this->active,
            'id'           => $this->id,
            'created_by'   => $this->created_by  
        ];
    }
    function formatPhoneNumber() { 
        $this->phone = preg_replace('/\D+/', '', $this->phone); 
    }
}
