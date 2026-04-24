<?php

namespace Core\Notifications\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use Core\Notifications\Application\DTOs\DeleteNotificationRequest;
use Core\Notifications\Domain\Services\NotificationDBService;
use Illuminate\Support\Facades\Event;

class DeleteNotification
{
    public function __construct(private NotificationDBService $serviceDB) {}

    public function handle(DeleteNotificationRequest $dto)
    {
        return $this->serviceDB->delete($dto->toArray());
    }
}