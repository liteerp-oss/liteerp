<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/dashboard/extensions/pusher-setting', function () {
        return view('PusherSetting::index');
    });
});
