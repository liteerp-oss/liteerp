<?php

namespace Core\ActivityLog\Domain\Services;

use Core\ActivityLog\Domain\Entities\ActivityLog;

interface ActivityLogService
{
    public function create(array $data): ActivityLog;
    public function getByEntityTypeAndEntityId(string $entityType, string $entityId): ActivityLog;
    public function index(array $data): array;
}