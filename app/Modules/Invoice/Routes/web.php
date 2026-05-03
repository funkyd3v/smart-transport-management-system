<?php

use App\Modules\Invoice\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('invoices', InvoiceController::class);
});
