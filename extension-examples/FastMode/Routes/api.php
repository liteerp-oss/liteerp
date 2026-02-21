<?php

use Extensions\FastMode\Http\Controllers\FastModeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['business'])
    ->prefix('api/extension')
    ->group(function () {
        Route::resource('fastmode', FastModeController::class);
    });
