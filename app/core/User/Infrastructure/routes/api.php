<?php

use Illuminate\Support\Facades\Route;
use Core\User\Http\Controllers\UserController;

Route::prefix('api/business-access')->middleware(['business'])->group(function () {
    Route::resource('/users', UserController::class);
});