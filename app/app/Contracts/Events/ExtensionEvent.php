<?php 
namespace App\Contracts\Events;
interface ExtensionEvent {
    function listener(string $eventName, callable $callback) : void;
    function dispatch(string $eventName, array $data);
}