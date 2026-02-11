<?php

namespace Core\Notifications\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Notifications\Domain\Services\NotificationDBService;
use Core\Notifications\Domain\Repositories\NotificationRepositoryInterface;
use Core\Notifications\Domain\Entities\Notification;

class NotificationDBServiceImpl implements NotificationDBService
{
    public function __construct(private NotificationRepositoryInterface $repo) {}

    public function create(array $data): Notification
    {
        $entity = Notification::fromArray($data);
        return $this->repo->create($entity);
    }
    public function insertMany(array $data): array
    {
        return $this->repo->insertMany($data);
    }
    public function index(array $data): array
    {
        return $this->repo->index($data);
    }
    public function update(array $data): Notification | BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("notifications::messages.not_found"));
        }
        $entity->markRead();
        return $this->repo->update($entity);
    }
    public function delete(array $data): Notification|BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("notifications::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
}