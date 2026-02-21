<?php

use Extensions\Smtp\Http\Controllers\Api\SmtpController;
use Illuminate\Support\Facades\Route;

Route::prefix("api/extensions/smtp")->middleware('business')->group(function () {
    Route::resource("/",SmtpController::class);
});
Route::prefix("api/extensions/smtp-test")->middleware('business')->group(function () {
    Route::post("/",[SmtpController::class,'send']);
});