<?php

namespace Core\Notifications\Application\DTOs;

use Illuminate\Support\Facades\URL;

class InsertManyNotificationRequest
{
    public function __construct(
        public ?string $title = null,
        public ?string $message = null,
        public ?string $link = null,
        public ?string $entity_type,
        public ?int $entity_id,
        public array $chanels = [],
        public ?string $queue = null,
        public ?int $business_id = null,
        public ?string $type = null,
        public int $user_id,
        // translate params
        public array $message_params = [],
        public array $title_params = []
    ) {
        
    }

    public static function fromArray(array $data): self
    {
        return new self(
            message: $data['message'] ?? null,
            link: $data['link'] ?? URL::to('/dashboard'),        
            title: $data['title'] ?? null,      
            entity_type: $data['entity_type'] ?? null,
            entity_id: $data['entity_id'] ?? null,
            chanels: $data['chanels']  ?? ['db'],
            queue: $data['queue'] ?? 'low',
            business_id: $data['business_id'] ?? null,
            type: $data['type'] ?? null,
            user_id: $data['user_id'],
            message_params: $data['message_params'] ?? [],
            title_params: $data['title_params'] ?? []
        );
    }
    
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'link'    => $this->link, 
            'title'   => $this->title,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'chanels' => $this->chanels,
            'queue'   => $this->queue,
            'business_id'   => $this->business_id,
            'type'  => $this->type,
            'user_id' => $this->user_id,
            'message_params' => $this->message_params,
            'title_params' => $this->title_params
        ];
    }
    public function getQueue():string {
        return $this->queue;
    }
}
