<?php

declare(strict_types=1);

use App\Modules\Driver\Http\Controllers\DashboardController;
use App\Modules\Driver\Http\Controllers\ProfileController;
use App\Modules\Driver\Http\Controllers\Trip\ReloadController;
use App\Modules\Driver\Http\Controllers\Trip\TripController;
use App\Modules\Driver\Http\Controllers\Trip\TripExpenseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:driver', 'throttle:60,1'])
    ->prefix('driver')
    ->as('driver.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
        Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');
        Route::patch('/trips/{trip}/status', [TripController::class, 'updateStatus'])->name('trips.update-status');
        Route::post('/trips/{trip}/expenses', [TripExpenseController::class, 'store'])->name('trips.expenses.store');
        Route::post('/trips/{trip}/reloads', [ReloadController::class, 'store'])->name('trips.reloads.store');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
