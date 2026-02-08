<?php

use Illuminate\Support\Facades\Route;
use Extensions\PusherSetting\Http\Controllers\Api\PusherSettingController;

Route::prefix('/api')->middleware(['isLogged', 'IsAdmin', 'app.language'])->group(function () {
    Route::get('/extensions/pusher-setting/config', [PusherSettingController::class, 'show']);
    Route::post('/extensions/pusher-setting/save', [PusherSettingController::class, 'save']);
});
