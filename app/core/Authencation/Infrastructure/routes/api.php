<?php

use Illuminate\Support\Facades\Route;
use Core\Authencation\Http\Controllers\AuthencationController;

Route::prefix('/api')->middleware(['app.language'])->group(function () {
    Route::prefix('authencation')->group(function(){
        Route::post('/', [AuthencationController::class,'login']);
        Route::post('/register', [AuthencationController::class,'register']);
        Route::post('/verify', [AuthencationController::class,'verify']);
        Route::put('/forget-password', [AuthencationController::class,'forgetPassword']);
        Route::put('/reset-password', [AuthencationController::class,'resetPassword']);
    });
    Route::prefix('authencation')->middleware(['app.isLogged'])->group(function(){
        Route::get('/profile', [AuthencationController::class,'profile']);
        Route::put('/update', [AuthencationController::class,'update']);
    });
});