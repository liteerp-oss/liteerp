<?php 
namespace App\Supports\Events;

use App\Contracts\Events\ExtensionEvent;
use Illuminate\Support\Facades\Event;

class ExtensionEventImpl implements ExtensionEvent {
    public function listenr(string $extension, string $eventName, callable $callback): void
    {
        Event::listen("erp.$extension.*",function(string $event, array $data) use($callback, $extension,$eventName) {
            $eventName = "erp.$extension." . $eventName;
            if($event === $eventName) {
                $callback($data);
            }
        });
    }
    public function dispatch(string $extension, string $eventName, array $data)
    {
        Event::dispatch("erp.$extension.$eventName",$data);
    }
}