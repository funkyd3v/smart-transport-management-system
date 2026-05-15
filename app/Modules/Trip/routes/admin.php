<?php

declare(strict_types=1);

use App\Modules\Trip\Http\Controllers\Admin\ExpenseController;
use App\Modules\Trip\Http\Controllers\Admin\InvoiceController;
use App\Modules\Trip\Http\Controllers\Admin\PaymentController;
use App\Modules\Trip\Http\Controllers\Admin\TripController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:admin,manager'])
    ->prefix('admin/trips')
    ->as('admin.trips.')
    ->group(function (): void {
        Route::get('/', [TripController::class, 'index'])->name('index');
        Route::get('/create', [TripController::class, 'create'])->name('create');
        Route::post('/', [TripController::class, 'store'])->name('store');
        Route::get('/{trip}', [TripController::class, 'show'])->name('show');
        Route::post('/status', [TripController::class, 'updateStatus'])->name('status.update');

        Route::get('/{tripUlid}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

        Route::get('/{tripUlid}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::post('/{tripUlid}/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    });

Route::middleware(['web', 'auth', 'role:admin,manager'])
    ->prefix('admin/invoices')
    ->as('admin.invoices.')
    ->group(function (): void {
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoiceUlid}', [InvoiceController::class, 'show'])->name('show');
    });
