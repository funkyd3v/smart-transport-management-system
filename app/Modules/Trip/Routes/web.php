<?php

use App\Modules\Trip\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('trips', TripController::class);
});
