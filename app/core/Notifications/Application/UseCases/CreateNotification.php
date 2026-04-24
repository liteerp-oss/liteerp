<?php

namespace Core\Notifications\Application\UseCases;

use App\Exceptions\BadException;
use App\Jobs\SendMailJob;
use Core\Notifications\Application\DTOs\CreateNotificationRequest;
use Core\Notifications\Domain\Services\NotificationDBService;
use Core\Notifications\Infrastructure\Broadcasts\NewNotificationBroadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CreateNotification
{
    public function __construct(private NotificationDBService $serviceDB) {}

    public function handle(CreateNotificationRequest $dto)
    {
        $create = null;
        DB::beginTransaction();
        foreach($dto->chanels as $key => $value ) {
            switch($value) {
                case "db":
                    $dto->message = $dto->message ? __($dto->message,$dto->message_params) : __("No data");
                    $dto->title = $dto->title ? __($dto->title,$dto->title_params) : $dto->entity_type;
                    $create = $this->serviceDB->create($dto->toArray());
                    NewNotificationBroadcast::dispatch($dto->user_id,$dto->business_id);
                    break;
                case "mail":
                    if(!$dto->title || !$dto->message) {
                        throw new BadException(__("notifications::messages.empty_title_message"));
                    }
                    SendMailJob::dispatch($dto->user_id,__($dto->title,$dto->title_params,$dto->locate) ?? __("No title"),
                        __($dto->message,$dto->message_params,$dto->locate),$dto->link ?? null)
                            ->onQueue($dto->queue ?? 'low');
                    
                    break;
            }
        }
        
        DB::commit();
        return $create;
    }
}