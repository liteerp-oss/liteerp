<?php 
namespace App\Contracts\Events;
interface ExtensionEvent {
    function listenr(string $extension, string $eventName, callable $callback) : void;
    function dispatch(string $extension, string $eventName, array $data);
}