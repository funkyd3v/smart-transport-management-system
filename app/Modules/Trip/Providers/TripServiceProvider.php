<?php

declare(strict_types=1);

namespace App\Modules\Trip\Providers;

use App\Modules\Payment\Events\PaymentSucceeded;
use App\Modules\Trip\Events\InvoiceGenerated;
use App\Modules\Trip\Events\TripStatusChanged;
use App\Modules\Trip\Listeners\CreateDueRecordOnInvoice;
use App\Modules\Trip\Listeners\SendTripNotification;
use App\Modules\Trip\Listeners\StopTripTrackingOnStatusChange;
use App\Modules\Trip\Listeners\UpdateDueOnPayment;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;
use App\Modules\Trip\Repositories\Contracts\VehicleLocationRepositoryInterface;
use App\Modules\Trip\Repositories\TripRepository;
use App\Modules\Trip\Repositories\VehicleLocationRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class TripServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TripRepositoryInterface::class, TripRepository::class);
        $this->app->bind(VehicleLocationRepositoryInterface::class, VehicleLocationRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'trip');

        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/driver.php');

        Event::listen(TripStatusChanged::class, SendTripNotification::class);
        Event::listen(TripStatusChanged::class, StopTripTrackingOnStatusChange::class);
        Event::listen(InvoiceGenerated::class, CreateDueRecordOnInvoice::class);
        Event::listen(PaymentSucceeded::class, UpdateDueOnPayment::class);
    }
}
