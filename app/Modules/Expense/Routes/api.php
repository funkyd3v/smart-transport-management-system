<?php

use App\Modules\Expense\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('expenses', ExpenseController::class);
});
