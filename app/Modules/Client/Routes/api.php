<?php

use App\Modules\Client\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('clients', ClientController::class);
});
