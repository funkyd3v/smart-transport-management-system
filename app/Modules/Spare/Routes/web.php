<?php

use App\Modules\Spare\Controllers\SpareController;
use App\Modules\Spare\Controllers\SpareSaleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:admin', 'throttle:60,1'])
    ->prefix('admin/spare')
    ->name('admin.spare.')
    ->group(function (): void {
        Route::resource('inventory', SpareController::class)->except(['show'])->parameters(['inventory' => 'inventory']);
        Route::resource('sales', SpareSaleController::class)->except(['edit', 'update']);

        Route::get('inventory/{part}/price', [SpareController::class, 'getPrice'])
            ->name('inventory.price');
    });
