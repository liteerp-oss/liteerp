<?php

namespace Core\Customer\Domain\Entities;

class Customer
{
    public ?int $id = null; // optional for existing records

    public function __construct(
        public int $business_id,
        public string $name,
        public ?string $contact_name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $tax_code = null,
        public ?string $national_id = null,
        public ?string $bank_name = null,
        public ?string $bank_account = null,
        public string $type = 'individual',   // individual | company
        public int $group,
        public ?string $website = null,
        public ?string $note = null,
        public bool $active = true
    ) {}

    /**
     * Create entity from array (useful for repository)
     */
    public static function fromArray(array $data): self
    {
        $entity = new self(
            business_id:  $data['business_id'],
            name:         $data['name'],
            contact_name: $data['contact_name'] ?? null,
            email:        $data['email'] ?? null,
            phone:        $data['phone'] ?? null,
            address:      $data['address'] ?? null,
            tax_code:     $data['tax_code'] ?? null,
            national_id:  $data['national_id'] ?? null,
            bank_name:    $data['bank_name'] ?? null,
            bank_account: $data['bank_account'] ?? null,
            type:         $data['type'] ?? 'individual',
            group:        $data['group'],
            website:      $data['website'] ?? null,
            note:         $data['note'] ?? null,
            active:       $data['active'] ?? true
        );

        // If ID exists, set it
        $entity->id = $data['id'] ?? null;

        return $entity;
    }

    /**
     * Convert entity to array (useful for saving to DB)
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'business_id'   => $this->business_id,
            'name'          => $this->name,
            'contact_name'  => $this->contact_name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'tax_code'      => $this->tax_code,
            'national_id'   => $this->national_id,
            'bank_name'     => $this->bank_name,
            'bank_account'  => $this->bank_account,
            'type'          => $this->type,
            'group'         => $this->group,
            'website'       => $this->website,
            'note'          => $this->note,
            'active'        => $this->active,
        ];
    }

    /**
     * Domain logic: change active status
     */
    public function deactivate(): void
    {
        $this->active = false;
    }

    public function activate(): void
    {
        $this->active = true;
    }
}
