<?php

declare(strict_types=1);

use App\Modules\Driver\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::apiResource('drivers', DriverController::class);
    });
