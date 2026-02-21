<?php 
namespace App\Supports\Events;

use App\Contracts\Events\ExtensionEvent;
use Illuminate\Support\Facades\Event;

class ExtensionEventImpl implements ExtensionEvent {
    public function listener(string $eventName, callable $callback): void
    {
        Event::listen($eventName,function(string $event, array $data) use($callback,$eventName) {
            if($event === $eventName) {
                $callback($data);
            }
        });
    }
    public function dispatch(string $eventName, array $data)
    {
        Event::dispatch($eventName,$data);
    }
}