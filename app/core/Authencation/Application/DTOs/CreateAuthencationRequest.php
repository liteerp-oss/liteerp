<?php

namespace Core\Authencation\Application\DTOs;

class CreateAuthencationRequest
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $name = null,
        public string $lang = 'en'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            name: $data['name'] ?? null,
            lang: $data['lang'] ?? 'en'
        );
    }
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'name'  => $this->name,
            'lang' => $this->lang,
        ];
    }
}