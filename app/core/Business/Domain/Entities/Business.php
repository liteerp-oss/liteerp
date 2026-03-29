<?php

namespace Core\Business\Domain\Entities;

class Business
{
    public function __construct(
        public string $name,
        public string $address,
        public ?string $tax_code = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $logo_url = null,
        public ?string $bank_name = null,
        public ?string $bank_account_number = null,
        public ?string $bank_account_name = null,
        public ?int $id = null 
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            address: $data['address'] ?? null,
            tax_code: $data['tax_code'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            logo_url: $data['logo_url'] ?? null,
            bank_name: $data['bank_name'] ?? null,
            bank_account_number: $data['bank_account_number'] ?? null,
            bank_account_name: $data['bank_account_name'] ?? null,
            id: $data['id'] ?? null 
        );
    }
    public function toArray() : array{
        return [
            'name' => $this->name,
            'address' => $this->address,
            'tax_code'  => $this->tax_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo_url'  => $this->logo_url,
            'bank_name' => $this->bank_name,
            'bank_account_number'   => $this->bank_account_number,
            'bank_account_name' => $this->bank_account_name,
            'id'    => $this->id
        ];
    }
}
