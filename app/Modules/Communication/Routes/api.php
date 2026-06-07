<?php

declare(strict_types=1);

use App\Modules\Communication\Http\Controllers\CommunicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/communications')
    ->middleware(['api', 'auth:sanctum', 'throttle:60,1'])
    ->as('api.communications.')
    ->group(function (): void {
        Route::post('/send', [CommunicationController::class, 'send'])->name('send');
        Route::post('/otp/generate', [CommunicationController::class, 'generateOtp'])->middleware('throttle:10,1')->name('otp.generate');
        Route::post('/otp/verify', [CommunicationController::class, 'verifyOtp'])->middleware('throttle:20,1')->name('otp.verify');
});
