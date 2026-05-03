<?php

use App\Modules\Spare\Controllers\SpareController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('spares', SpareController::class);
});
