<?php

use Core\Permission\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api/business-access')->middleware(['business'])->group(function () {
    Route::resource('/permissions', PermissionController::class);
});
