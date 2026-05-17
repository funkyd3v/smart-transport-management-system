<?php

declare(strict_types=1);

namespace App\Modules\Cashbook\Providers;

use App\Modules\Cashbook\Listeners\RecordExpenseInCashbook;
use App\Modules\Cashbook\Listeners\RecordPaymentInCashbook;
use App\Modules\Cashbook\Repositories\CashbookRepository;
use App\Modules\Cashbook\Repositories\CashbookRepositoryInterface;
use App\Modules\Trip\Events\PaymentRecorded;
use App\Modules\Trip\Models\TripExpense;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CashbookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CashbookRepositoryInterface::class, CashbookRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'cashbook');

        Event::listen(PaymentRecorded::class, RecordPaymentInCashbook::class);

        Event::listen('eloquent.created: '.TripExpense::class, function (TripExpense $expense): void {
            app(RecordExpenseInCashbook::class)->handle($expense);
        });
    }
}
