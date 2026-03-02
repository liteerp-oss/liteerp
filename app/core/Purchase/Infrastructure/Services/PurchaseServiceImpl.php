<?php

namespace Core\Purchase\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Purchase\Domain\Services\PurchaseService;
use Core\Purchase\Domain\Repositories\PurchaseRepositoryInterface;
use Core\Purchase\Domain\Entities\Purchase;

class PurchaseServiceImpl implements PurchaseService
{
    public function __construct(private PurchaseRepositoryInterface $repo) {}

    public function create(array $data): Purchase
    {
        $entity = Purchase::fromArray($data);
        $entity->approved_by = null;
        return $this->repo->create($entity);
    }
    public function show(array $data): array | BadException
    {
        return $this->repo->findByIdWithFullData($data) ?? throw new BadException(__("purchase::messages.not_found"));
    }
    public function findOneById(array $data): Purchase|BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("purchase::messages.not_found")); 
    }
    public function update(array $data): Purchase|BadException
    {

        $row = $this->repo->findById($data);
        if(!$row) {
            throw new BadException(__("purchase::messages.not_found"));
        }
        switch ($data['status']) {
            case "draft":
                if (!$row->isDraft()) {
                    throw new BadException(__("purchase::messages.status_transition_invalid"));
                }
                /**
                 * Only can change information purchase while it has not yet change status
                 */
                $row->setDraft();
                $row->supplier_id = $data['supplier_id'] ?? $row->supplier_id;
                $row->purchase_date = $data['purchase_date'] ?? $row->purchase_date;
                $row->expected_date = $data['expected_date'] ?? $row->expected_date;
                $row->note = $data['note'] ?? $row->note;
                $row->shipping_fee = $data['shipping_fee'] ?? $row->shipping_fee;
                $row->payment_method = $data['payment_method'] ?? $row->payment_method;
                break;
            case "requested":
                if (!$row->isDraft()) {
                    throw new BadException(__("purchase::messages.status_transition_invalid"));
                }
                $row->setRequested();
                break;
            case "approved":
                if (!$row->isRequested()) {
                    throw new BadException(__("purchase::messages.status_transition_invalid"));
                }
                $row->setApproved();
                $row->approved_by = $data['approved_by'];
                break;
            case "cancelled":
                $row->setCancelled();
                break;
            default:
                throw new BadException(__("purchase::messages.status_invalid"));
                break;
        }
        return $this->repo->update($row);
    }
}
