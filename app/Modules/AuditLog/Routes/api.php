<?php

use App\Modules\AuditLog\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::apiResource('audit-logs', AuditLogController::class);
});
