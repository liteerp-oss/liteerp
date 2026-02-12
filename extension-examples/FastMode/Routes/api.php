<?php

use Extensions\FastMode\Http\Controllers\FastModeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['business'])
    ->prefix('api/extension/fastmode')
    ->group(function () {
        Route::resource('', FastModeController::class);
    });
