<?php

declare(strict_types=1);

use App\Modules\Cashbook\Controllers\CashbookController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/cashbook')
    ->middleware(['api', 'auth:sanctum'])
    ->as('api.cashbooks.')
    ->group(function (): void {
        Route::get('/', [CashbookController::class, 'index'])->name('index');
        Route::post('/', [CashbookController::class, 'store'])->name('store');
        Route::get('/{id}', [CashbookController::class, 'show'])->name('show');
        Route::delete('/{id}', [CashbookController::class, 'destroy'])->name('destroy');
    });
