<?php

namespace Core\ActivityLog\Infrastructure\Services;

use Core\ActivityLog\Domain\Services\ActivityLogService;
use Core\ActivityLog\Domain\Repositories\ActivityLogRepositoryInterface;
use Core\ActivityLog\Domain\Entities\ActivityLog;
use Core\ActivityLog\Infrastructure\Helpers\AuditDiffBuilder;

class ActivityLogServiceImpl implements ActivityLogService
{
    public function __construct(private ActivityLogRepositoryInterface $repo) {}

    public function create(array $data): ActivityLog
    {
        $entity = ActivityLog::fromArray($data);
        $old = $this->repo->getByEntityTypeAndEntityId($entity->entity_type, $entity->entity_id);
        if($old){
            $diff = AuditDiffBuilder::make($old->description, $entity->description);
            $entity->description = json_encode([
                ...json_decode($entity->description, true),
                'diff' => $diff->compare()
                        ->toArray()
            ]);
        }
        return $this->repo->create($entity);
    }
    public function getByEntityTypeAndEntityId(string $entityType, string $entityId): ActivityLog
    {
        return $this->repo->getByEntityTypeAndEntityId($entityType, $entityId);
    }
    public function index(array $data) : array {
        return $this->repo->index($data);
    }
}