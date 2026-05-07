<?php

declare(strict_types=1);

use App\Modules\Manager\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('manager')
    ->as('manager.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::view('/clients', 'manager::pages.clients.index')->name('clients.index');
        Route::view('/clients/create', 'manager::pages.clients.create')->name('clients.create');
        Route::view('/clients/edit', 'manager::pages.clients.edit')->name('clients.edit');
        Route::view('/clients/profile', 'manager::pages.clients.show')->name('clients.show');
        Route::view('/drivers', 'manager::drivers.index')->name('drivers.index');
        Route::view('/drivers/create', 'manager::drivers.create')->name('drivers.create');
        Route::view('/drivers/profile', 'manager::drivers.show')->name('drivers.show');
        Route::view('/drivers/edit', 'manager::drivers.edit')->name('drivers.edit');
        Route::view('/trucks', 'manager::trucks.index')->name('trucks.index');
        Route::view('/trucks/create', 'manager::trucks.create')->name('trucks.create');
        Route::view('/trucks/profile', 'manager::trucks.show')->name('trucks.show');
        Route::view('/trucks/edit', 'manager::trucks.edit')->name('trucks.edit');
    });
