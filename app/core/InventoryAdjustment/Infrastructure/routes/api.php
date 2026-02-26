<?php

use Illuminate\Support\Facades\Route;
use Core\InventoryAdjustment\Http\Controllers\InventoryAdjustmentController;
use Core\InventoryAdjustment\Http\Controllers\ViewInventoryAdjustmentController;

Route::prefix('/api/business-access')->middleware(['business'])->group(function () {
    Route::resource('/inventory-adjustments', InventoryAdjustmentController::class);
    Route::get('/view/inventory-adjustments', [ViewInventoryAdjustmentController::class,'index']);
});