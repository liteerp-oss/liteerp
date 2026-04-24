<?php

namespace Core\Notifications\Application\UseCases;

use App\Jobs\SendMailJob;
use Core\Notifications\Application\DTOs\CreateNotificationRequest;
use Core\Notifications\Application\DTOs\InsertManyNotificationRequest;
use Core\Notifications\Domain\Entities\Notification;
use Core\Notifications\Domain\Services\NotificationDBService;
use Core\Notifications\Infrastructure\Broadcasts\NewNotificationBroadcast;
use Core\Permission\Application\UseCases\GetUsersByPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class InsertManyNotification
{
    public function __construct(private NotificationDBService $serviceDB,
    private GetUsersByPermission $getUsersByPermission) {}

    public function handle(InsertManyNotificationRequest $dto)
    {
        $create = [];
        DB::beginTransaction();
        $users = $this->getUsersByPermission->handle($dto->toArray());
        logs()->debug("users",$users);
        foreach($users as $k => $user ) {
            foreach($dto->chanels as $key => $chanels) {
                $adapter = new CreateNotificationRequest(
                    user_id: $user['user_id'],
                    message: $dto->message ? __($dto->message,$dto->message_params,$user['lang']) : __("No message"), 
                    link: $dto->link,
                    title: $dto->title ? __($dto->title,$dto->title_params,$user['lang']) : __("No title"),
                    entity_type: $dto->entity_type,
                    entity_id: $dto->entity_id,
                    chanels: $dto->chanels,
                    type: $dto->type,
                    business_id: $dto->business_id,
                    queue: $dto->queue,
                    locate: $user['lang']
                );
                switch($chanels) {
                    case "db":
                        $entity = Notification::fromArray($adapter->toArray());
                        $create[$k] = [
                            ...$entity->toArray(),
                            'created_at' => date('Y-m-d H:i:s',time()),
                            'updated_at' => date('Y-m-d H:i:s',time()),
                        ];
                        NewNotificationBroadcast::dispatch($user['user_id'],$dto->business_id);
                        break;
                    case "mail":
                        SendMailJob::dispatch($adapter->user_id,$adapter->title,
                            $adapter->message,$adapter->link ?? URL::to('/dashboard'))
                                ->onQueue($adapter->queue ?? 'low');
                        break;
                }
            }
        }
        logs()->debug("InsertManyNotification: ".json_encode($create));
        $data = $this->serviceDB->insertMany($create);

        DB::commit();
        return $data;
    }
}