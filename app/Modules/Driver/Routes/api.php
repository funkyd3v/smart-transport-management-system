<?php

declare(strict_types=1);

use App\Modules\Driver\Controllers\DriverController;
use App\Modules\Driver\Http\Controllers\Api\TripLocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::apiResource('drivers', DriverController::class);
    });

Route::prefix('api/driver')
    ->middleware(['api', 'auth:sanctum', 'role:driver', 'throttle:trip-location'])
    ->as('driver.api.')
    ->group(function (): void {
        Route::post('/trips/{trip}/location', [TripLocationController::class, 'store'])->name('trips.location.store');
    });
