<?php

use Illuminate\Support\Facades\Route;
use Core\CustomerGroup\Http\Controllers\CustomerGroupController;
use Core\CustomerGroup\Http\Controllers\ViewController;

Route::prefix('/api/business-access')->middleware(['business'])->group(function () {
    Route::resource('/customer-groups', CustomerGroupController::class);
    Route::get('/view/customer-groups', [ViewController::class,'index']);
});