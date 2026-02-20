<?php

use Core\PermissionGroup\Http\Controllers\ViewPermissionGroupController;
use Core\PermissionGroup\Http\Controllers\PermissionGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api/business-access')->middleware(['business'])->group(function () {
    Route::resource('/permission-groups', PermissionGroupController::class);
    Route::get('/view/permission-groups', [ViewPermissionGroupController::class, 'index']);
});
