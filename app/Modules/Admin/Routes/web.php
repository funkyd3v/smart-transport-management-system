<?php

declare(strict_types=1);

use App\Modules\Admin\Http\Controllers\AuditLogController;
use App\Modules\Admin\Http\Controllers\ClientController;
use App\Modules\Admin\Http\Controllers\DashboardController;
use App\Modules\Admin\Http\Controllers\DriverController;
use App\Modules\Admin\Http\Controllers\FinanceController;
use App\Modules\Admin\Http\Controllers\ProfileController;
use App\Modules\Admin\Http\Controllers\ReportController;
use App\Modules\Admin\Http\Controllers\SettingsController;
use App\Modules\Admin\Http\Controllers\SpareController;
use App\Modules\Admin\Http\Controllers\SpareSaleController;
use App\Modules\Admin\Http\Controllers\TripController;
use App\Modules\Admin\Http\Controllers\TruckController;
use App\Modules\Admin\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:admin', 'throttle:60,1'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class)->except(['create', 'store']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
        Route::patch('users/bulk-approve', [UserController::class, 'bulkApprove'])->name('users.bulkApprove');

        Route::resource('trips', TripController::class);
        Route::patch('trips/{trip}/force-complete', [TripController::class, 'forceComplete'])->name('trips.forceComplete');
        Route::patch('trips/{trip}/override-status', [TripController::class, 'overrideStatus'])->name('trips.overrideStatus');
        Route::patch('trips/{trip}/reassign', [TripController::class, 'reassign'])->name('trips.reassign');

        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/dues', [FinanceController::class, 'dues'])->name('finance.dues');
        Route::post('/finance/dues/{due}/payment', [FinanceController::class, 'recordPayment'])->name('finance.dues.payment');
        Route::get('/finance/cashbook', [FinanceController::class, 'cashbook'])->name('finance.cashbook');

        Route::resource('drivers', DriverController::class);
        Route::patch('drivers/{driver}/toggle-status', [DriverController::class, 'toggleStatus'])->name('drivers.toggleStatus');
        Route::patch('drivers/{driver}/rating', [DriverController::class, 'updateRating'])->name('drivers.rating');

        Route::resource('trucks', TruckController::class);
        Route::patch('trucks/{truck}/status', [TruckController::class, 'updateStatus'])->name('trucks.status');

        Route::resource('clients', ClientController::class);
        Route::get('clients/{client}/due-pdf', [ClientController::class, 'duePdf'])->name('clients.duePdf');

        Route::resource('spare', SpareController::class);
        Route::resource('spare/sales', SpareSaleController::class)->names('spare.sales');

        Route::prefix('profile')->name('profile.')->group(function (): void {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/personal', [ProfileController::class, 'updatePersonal'])->name('personal.update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:5,1')->name('password.update');
            Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
            Route::put('/company', [ProfileController::class, 'updateCompany'])->name('company.update');
            Route::post('/company/logo', [ProfileController::class, 'updateCompanyLogo'])->name('company.logo');
            Route::post('/company/signature', [ProfileController::class, 'updateCompanySignature'])->name('company.signature');
            Route::patch('/notifications', [ProfileController::class, 'updateNotification'])->name('notifications.update');
            Route::get('/stats', [ProfileController::class, 'stats'])->name('stats');
            Route::get('/sessions', [ProfileController::class, 'sessions'])->name('sessions');
            Route::delete('/sessions', [ProfileController::class, 'destroySessions'])->name('sessions.destroy');
            Route::get('/activity', [ProfileController::class, 'activityLog'])->name('activity');
            Route::get('/activity/export', [ProfileController::class, 'exportActivity'])->name('activity.export');
        });

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/reports/download/{type}', [ReportController::class, 'download'])->name('reports.download');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

        Route::middleware(['password.confirm'])->group(function (): void {
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::patch('/settings/{section}', [SettingsController::class, 'update'])->name('settings.update');
        });
    });
