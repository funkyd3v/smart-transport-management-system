<?php

use App\Modules\Driver\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('drivers', DriverController::class);
});
