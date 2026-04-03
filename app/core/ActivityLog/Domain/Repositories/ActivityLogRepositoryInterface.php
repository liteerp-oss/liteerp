<?php

namespace Core\ActivityLog\Domain\Repositories;

use Core\ActivityLog\Domain\Entities\ActivityLog;

interface ActivityLogRepositoryInterface
{
    public function create(ActivityLog $entity): ActivityLog;
    public function getByEntityTypeAndEntityId(string $entityType, string $entityId): ?ActivityLog;
    public function index(array $data) : array;
}