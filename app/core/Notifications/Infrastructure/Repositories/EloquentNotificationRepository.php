<?php

namespace Core\Notifications\Infrastructure\Repositories;

use App\Models\NotificationModel;
use Core\Notifications\Domain\Repositories\NotificationRepositoryInterface;
use Core\Notifications\Domain\Entities\Notification;
use Illuminate\Support\Facades\DB;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function create(Notification $entity): Notification
    {
        $create = NotificationModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function insertMany(array $data): array
    {
        NotificationModel::insert($data);
        return $data;
    }
    public function index(array $data) : array {
        if(!empty($data['is_not_read'])) {
            return NotificationModel::select(DB::raw("COUNT(user_id) as total"))
            ->where('user_id',$data['user_id'])
            ->where('is_read',false)
            ->where('business_id',$data['business_id'])
            ->first()?->toArray();
        }
        if(!empty($data['get_type'])) {
            return NotificationModel::select("entity_type")
            ->where('user_id',$data['user_id'])
            ->where('business_id',$data['business_id'])
            ->groupBy("entity_type")
            ->get()?->toArray();
        }
        $index = NotificationModel::where('user_id',$data['user_id'])
        ->where('business_id',$data['business_id']);
        if(!empty($data['type'])) {
            $index = $index->where('entity_type',$data['type']);
        }
        return $index->orderBy("id","DESC")->paginate(15)->toArray();
    }
    public function findById(array $data): ?Notification
    {
        $row = NotificationModel::where('id',$data['id'])
        ->where('user_id',$data['user_id'])
        ->where('business_id',$data['business_id'])
        ->first()?->toArray();
        if(!$row) {
            return null;
        }
        return Notification::fromArray($row);
    }
    public function update(Notification $entity): Notification
    {
        NotificationModel::where('id',$entity->id)
        ->where('user_id',$entity->user_id)
        ->where('business_id',$entity->business_id)->update($entity->toArray());
        return $entity;
    }
    public function delete(Notification $entity): Notification
    {
        NotificationModel::where('id',$entity->id)
        ->where('user_id',$entity->user_id)
        ->where('business_id',$entity->business_id)
        ->delete();
        return $entity;
    }
}