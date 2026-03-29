<?php

namespace Core\Business\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Business\Domain\Services\BusinessService;
use Core\Business\Domain\Repositories\BusinessRepositoryInterface;
use Core\Business\Domain\Entities\Business;

class BusinessServiceImpl implements BusinessService
{
    public function __construct(private BusinessRepositoryInterface $repo) {}

    public function create(array $data): Business
    {
        if($this->repo->findByName($data)) {
            throw new BadException(__("business::messages.name_used"));
        }
        $entity = Business::fromArray($data);
        if($this->repo->checkExists($entity)) {
            throw new BadException(__("business::messages.name_address_used"));
        }
        return $this->repo->create($entity);
    }
    public function show(array $data): array | BadException
    {
        return $this->repo->findByIdWithFullData($data) ?? throw new BadException(__("business::messages.not_found"));
    }
    public function update(array $data): Business
    {
        $entity = $this->repo->findByName($data);
        if($entity) {
            if($data['id'] !== $entity->id) {
                throw new BadException(__("business::messages.name_used"));
            } 
        } else {
            $entity = $this->repo->findById($data);
        }
        
        $entity->name = $data['name'] ?? $entity->name;
        $entity->address = $data['address'] ?? $entity->address;
        $entity->tax_code = $data['tax_code'] ?? $entity->tax_code;
        $entity->phone = $data['phone'] ?? $entity->phone;
        $entity->email = $data['email'] ?? $entity->email;
        $entity->logo_url = $data['logo_url'] ?? $entity->logo_url;
        $entity->bank_name = $data['bank_name'] ?? $entity->bank_name;
        $entity->bank_account_number = $data['bank_account_number'] ?? $entity->bank_account_number;
        $entity->bank_account_name = $data['bank_account_name'] ?? $entity->bank_account_name;
        return $this->repo->update($entity);
    }
    public function all() : array {
        return $this->repo->all();
    }
    public function getById(array $data): ?Business
    {
        return $this->repo->findById($data);
    }
}