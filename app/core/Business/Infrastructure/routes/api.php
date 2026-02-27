<?php

use Core\Business\Http\Controllers\BusinessController;
use Core\Business\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api')->middleware(['app.isLogged', 'app.language'])->group(function () {
    Route::get('/view/business', [ViewController::class, 'index']);
    Route::get('/business', [BusinessController::class, 'index']);
    Route::get('/business/{id}', [BusinessController::class, 'show']);
    Route::middleware(['business'])->group(function () {
        Route::put('/business/{id}', [BusinessController::class, 'update']);
    });
    Route::middleware(['app.isAdmin'])->group(function () {
        Route::post('/business', [BusinessController::class, 'store']);
    });
});
