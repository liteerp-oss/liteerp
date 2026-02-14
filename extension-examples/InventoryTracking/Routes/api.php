<?php

use Extensions\InventoryTracking\Http\Controllers\InventoryTrackingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['business'])
    ->prefix('api/extension/inventory')
    ->group(function () {
        Route::get('/', [InventoryTrackingController::class, 'index']);
        Route::post('/', [InventoryTrackingController::class, 'store']);
    });
