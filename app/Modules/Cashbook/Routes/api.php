<?php

use App\Modules\Cashbook\Controllers\CashbookController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('cashbooks', CashbookController::class);
});
