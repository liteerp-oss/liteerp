<?php

use Illuminate\Support\Facades\Route;
use Extensions\PusherSetting\Http\Controllers\Api\PusherSettingController;

Route::prefix('/api/pusher-setting')->middleware(['isLogged', 'IsAdmin', 'app.language'])->group(function () {
    Route::get('/config', [PusherSettingController::class, 'show']);
    Route::post('/save', [PusherSettingController::class, 'save']);
});
