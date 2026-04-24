<?php

namespace Core\Notifications\Application\UseCases;

use Core\Notifications\Application\DTOs\IndexNotificationRequest;
use Core\Notifications\Domain\Services\NotificationDBService;

class IndexNotification
{
    public function __construct(private NotificationDBService $service) {}

    public function handle(IndexNotificationRequest $dto)
    {
        return $this->service->index($dto->toArray());
    }
}