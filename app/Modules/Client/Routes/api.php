<?php

declare(strict_types=1);

use App\Modules\Client\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::apiResource('clients', ClientController::class);
    });
