<?php

use Extensions\InvoiceOutPDF\Http\Controllers\InvoiceOutPDFController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('extensions/invoice-out-pdf')
    ->group(function () {
        Route::get('/', function () {
            return "Not found";
        })->name('InvoiceOutPDF');
        Route::get('/{invoiceId}', [InvoiceOutPDFController::class, 'show'])
            ->name('InvoiceOutPDF.show');
    });
