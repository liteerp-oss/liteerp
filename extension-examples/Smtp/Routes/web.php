<?php

use Extensions\Smtp\Http\Controllers\SmtpController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('dashboard/extensions/smtp')
    ->group(function () {
        Route::get('/', [SmtpController::class, 'index'])->name('smtp-extension');
    });
