<?php

declare(strict_types=1);

use App\Modules\Manager\Http\Controllers\Client\ClientController;
use App\Modules\Manager\Http\Controllers\DashboardController;
use App\Modules\Manager\Http\Controllers\Trip\TripController;
use App\Modules\Manager\Http\Controllers\Trip\TripExpenseController;
use App\Modules\Manager\Http\Controllers\Trip\TripInvoiceController;
use App\Modules\Manager\Http\Controllers\Trip\TripPaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('manager')
    ->as('manager.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::middleware(['throttle:60,1'])->group(function (): void {
            Route::resource('clients', ClientController::class);
            Route::patch('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
                ->name('clients.toggle-status');
        });

        Route::view('/drivers', 'manager::drivers.index')->name('drivers.index');
        Route::view('/drivers/create', 'manager::drivers.create')->name('drivers.create');
        Route::view('/drivers/profile', 'manager::drivers.show')->name('drivers.show');
        Route::view('/drivers/edit', 'manager::drivers.edit')->name('drivers.edit');
        Route::view('/trucks', 'manager::trucks.index')->name('trucks.index');
        Route::view('/trucks/create', 'manager::trucks.create')->name('trucks.create');
        Route::view('/trucks/profile', 'manager::trucks.show')->name('trucks.show');
        Route::view('/trucks/edit', 'manager::trucks.edit')->name('trucks.edit');

        Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
        Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create');
        Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
        Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');
        Route::post('/trips/status', [TripController::class, 'updateStatus'])->name('trips.status.update');

        Route::get('/trips/{tripUlid}/payments/create', [TripPaymentController::class, 'create'])->name('trips.payments.create');
        Route::post('/trips/payments', [TripPaymentController::class, 'store'])->name('trips.payments.store');

        Route::get('/trips/{tripUlid}/expenses/create', [TripExpenseController::class, 'create'])->name('trips.expenses.create');
        Route::post('/trips/expenses', [TripExpenseController::class, 'store'])->name('trips.expenses.store');

        Route::post('/trips/invoices', [TripInvoiceController::class, 'store'])->name('trips.invoices.store');
        Route::get('/trips/invoices/{invoiceUlid}', [TripInvoiceController::class, 'show'])->name('trips.invoices.show');
    });
