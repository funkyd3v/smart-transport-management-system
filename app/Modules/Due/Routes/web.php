<?php

use App\Modules\Due\Controllers\DueController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('dues', DueController::class);
});
