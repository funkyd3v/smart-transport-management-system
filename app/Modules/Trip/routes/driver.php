<?php

declare(strict_types=1);

use App\Modules\Trip\Http\Controllers\Driver\TripController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:driver'])
    ->prefix('driver/trips')
    ->as('driver.trips.')
    ->group(function (): void {
        Route::get('/', [TripController::class, 'index'])->name('index');
        Route::get('/{tripUlid}', [TripController::class, 'show'])->name('show');
        Route::post('/status', [TripController::class, 'updateStatus'])->name('status.update');
        Route::post('/{tripUlid}/reload', [TripController::class, 'reload'])->name('reload.store');
    });
