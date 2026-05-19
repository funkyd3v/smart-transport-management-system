<?php

declare(strict_types=1);

use App\Modules\Manager\Http\Controllers\Client\ClientController;
use App\Modules\Manager\Http\Controllers\DashboardController;
use App\Modules\Manager\Http\Controllers\Driver\DriverController;
use App\Modules\Manager\Http\Controllers\ProfileController;
use App\Modules\Manager\Http\Controllers\Trip\TripController;
use App\Modules\Manager\Http\Controllers\Trip\TripExpenseApprovalController;
use App\Modules\Manager\Http\Controllers\Trip\TripExpenseController;
use App\Modules\Manager\Http\Controllers\Trip\TripInvoiceController;
use App\Modules\Manager\Http\Controllers\Trip\TripPaymentController;
use App\Modules\Manager\Http\Controllers\Truck\TruckController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('manager')
    ->as('manager.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::middleware(['throttle:60,1'])->group(function (): void {
            Route::resource('clients', ClientController::class);
            Route::patch('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
                ->name('clients.toggle-status');

            Route::resource('drivers', DriverController::class);
            Route::patch('drivers/{driver}/toggle-status', [DriverController::class, 'toggleStatus'])
                ->name('drivers.toggle-status');
            Route::patch('drivers/{driver}/toggle-approval', [DriverController::class, 'toggleApproval'])
                ->name('drivers.toggle-approval');

            Route::resource('trucks', TruckController::class);
            Route::patch('trucks/{truck}/status', [TruckController::class, 'updateStatus'])
                ->name('trucks.update-status');

            Route::resource('trips', TripController::class)->except(['edit', 'update', 'destroy']);
            Route::post('trips/{trip}/expenses', [TripExpenseController::class, 'store'])->name('trips.expenses.store');
            Route::post('trips/{trip}/expenses/{expense}/approve', [TripExpenseApprovalController::class, 'approve'])->name('trips.expenses.approve');
            Route::post('trips/{trip}/expenses/{expense}/reject', [TripExpenseApprovalController::class, 'reject'])->name('trips.expenses.reject');
            Route::post('trips/{trip}/payments', [TripPaymentController::class, 'store'])->name('trips.payments.store');
            Route::get('trips/{trip}/invoice', [TripInvoiceController::class, 'showByTrip'])->name('trips.invoice.show');
            Route::patch('trips/{trip}/status', [TripController::class, 'updateStatus'])->name('trips.update-status');
        });
    });
