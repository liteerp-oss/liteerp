<?php

namespace Core\Supplier\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Supplier\Domain\Services\SupplierService;
use Core\Supplier\Domain\Repositories\SupplierRepositoryInterface;
use Core\Supplier\Domain\Entities\Supplier;

class SupplierServiceImpl implements SupplierService
{
    public function __construct(private SupplierRepositoryInterface $repo) {}

    public function create(array $data): Supplier | BadException
    {
        $entity = $this->repo->findByName($data);
        if($entity) {
            throw new BadException(__("supplier::messages.name_used"));
        }
        $entity = Supplier::fromArray($data);

        return $this->repo->create($entity);
    }
    public function update(array $data) : Supplier | BadException {
        $entity = $this->repo->findByName($data);
        if($entity && $entity->id !== $data['id']) {
            throw new BadException(__("supplier::messages.name_used"));
        }
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("supplier::messages.not_found"));
        }
        $entity->unit_name = $data['unit_name'] ?? $entity->unit_name;
        $entity->email = $data['email'] ?? $entity->email;
        $entity->phone = $data['phone'] ?? $entity->phone;
        $entity->address = $data['address'] ?? $entity->address;
        $entity->tax_code = $data['tax_code'] ?? $entity->tax_code;
        $entity->bank_name = $data['bank_name'] ?? $entity->bank_name;
        $entity->bank_account = $data['bank_account'] ?? $entity->bank_account;
        $entity->website = $data['website'] ?? $entity->website;
        $entity->note = $data['note'] ?? $entity->note;
        $entity->active = $data['active'] ?? $entity->active;
        return $this->repo->update($entity);
    }
    public function delete(array $data) : Supplier | BadException {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("supplier::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
}
