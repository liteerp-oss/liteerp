<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes([
    'middleware' => ['auth:sanctum','business'],
]);
Broadcast::channel(
    'user.{user_id}.{business_id}',
    function ($user, $user_id, $business_id) {
        $requested = request()->all();
        return $user->id === intval($user_id)
        && $requested['business_id'] == $business_id;
    }
);
