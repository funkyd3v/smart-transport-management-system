<?php

declare(strict_types=1);

use App\Modules\Client\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:client'])
    ->prefix('client')
    ->as('client.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });
