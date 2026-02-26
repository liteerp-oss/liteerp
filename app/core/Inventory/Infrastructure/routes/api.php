<?php

use Illuminate\Support\Facades\Route;
use Core\Inventory\Http\Controllers\InventoryController;
use Core\Inventory\Http\Controllers\ViewInventoryController;

Route::prefix('/api/business-access')->middleware(['business'])->group(function () {
    Route::resource('/inventory', InventoryController::class);
    Route::get('/view/inventory', [ViewInventoryController::class,'index']);
});