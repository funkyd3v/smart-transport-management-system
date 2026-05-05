<?php

declare(strict_types=1);

use App\Modules\Driver\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:driver'])
    ->prefix('driver')
    ->as('driver.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });
