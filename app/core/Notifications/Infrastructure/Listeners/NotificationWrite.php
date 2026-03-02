<?php

namespace Core\Notifications\Infrastructure\Listeners;

use App\Jobs\CreateNotificationJob;
use Core\Notifications\Application\DTOs\CreateNotificationRequest;
use Core\Notifications\Application\DTOs\InsertManyNotificationRequest;
use Core\Notifications\Application\UseCases\CreateNotification;
use Illuminate\Support\Facades\Event;

class NotificationWrite
{
    function __construct(private CreateNotification $CreateNotification)
    {
        
    }
    public function handle()
    {

        Event::listen("erp.notification.*", function (string $eventName, array $data) {
            if ($eventName === 'erp.notification.many') {
                $notiAdapter = InsertManyNotificationRequest::fromArray($data);

                CreateNotificationJob::dispatch($notiAdapter->toArray())->onQueue($notiAdapter->getQueue());
            } else if ($eventName === 'erp.notification.create') {
                
                $notiAdapter = CreateNotificationRequest::fromArray($data);
                $this->CreateNotification->handle($notiAdapter);
            }
        });
    }
}
