<?php

use App\Modules\Truck\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('trucks', TruckController::class);
});
