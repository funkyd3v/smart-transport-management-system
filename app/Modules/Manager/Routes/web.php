<?php

declare(strict_types=1);

use App\Modules\Manager\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('manager')
    ->as('manager.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });
