<?php

namespace Core\Notifications\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use Core\Notifications\Application\DTOs\UpdateNotificationRequest;
use Core\Notifications\Domain\Services\NotificationDBService;
use Illuminate\Support\Facades\Event;

class UpdateNotification
{
    public function __construct(private NotificationDBService $serviceDB) {}

    public function handle(UpdateNotificationRequest $dto)
    {
        return $this->serviceDB->update($dto->toArray());
    }
}