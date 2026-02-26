<?php

use Core\Warehouse\Http\Controllers\ViewWarehouseController;
use Illuminate\Support\Facades\Route;
use Core\Warehouse\Http\Controllers\WarehouseController;

Route::prefix('/api/business-access')->middleware(['business'])->group(function () {
    Route::resource('/warehouse', WarehouseController::class);
    Route::get('/view/warehouse', [ViewWarehouseController::class,'index']);
});