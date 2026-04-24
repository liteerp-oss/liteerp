<?php

namespace Core\ActivityLog\Infrastructure\Listeners;

use App\Exceptions\BadException;
use Core\ActivityLog\Application\DTOs\CreateActivityLogRequest;
use Core\ActivityLog\Application\UseCases\CreateActivityLog;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ActivityLogWrite
{
    public function __construct(private CreateActivityLog $createLog) {}
    public function handle()
    {
        Event::listen('erp.activitylog.*', function (string $eventName, array $data) {
            if (
                empty($data['user_id'])
                || empty($data['business_id'])
                || empty($data['id'])
            ) {
                return;
            }
            $this->createLog->handle(CreateActivityLogRequest::fromArray([
                'user_id' => $data['user_id'],
                'action' => str_replace('erp.activitylog.', '', $eventName),
                'description' => $data['data'] ?? [],
                'entity_type' => $data['type'],
                'entity_id' => $data['id'],
                'business_id' => $data['business_id']
            ]));
        });
    }
}
