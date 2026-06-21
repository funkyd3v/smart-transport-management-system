<?php

use App\Modules\Payment\Controllers\ClientBkashPaymentController;
use App\Modules\Payment\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('payments', PaymentController::class);
});

Route::middleware(['web', 'auth', 'role:client'])
    ->prefix('client/payments')
    ->as('client.payments.')
    ->controller(ClientBkashPaymentController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('/bkash/initiate', 'initiate')->middleware('throttle:20,1')->name('bkash.initiate');
        Route::get('/bkash/callback', 'callback')->middleware('throttle:60,1')->name('bkash.callback');
    });
