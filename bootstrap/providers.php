<?php

declare(strict_types=1);

use App\Modules\Admin\Providers\AdminServiceProvider;
use App\Modules\Cashbook\Providers\CashbookServiceProvider;
use App\Modules\Client\Providers\ClientServiceProvider;
use App\Modules\Communication\Providers\CommunicationServiceProvider;
use App\Modules\Driver\Providers\DriverServiceProvider;
use App\Modules\Manager\Providers\ManagerServiceProvider;
use App\Modules\Payment\Providers\PaymentServiceProvider;
use App\Modules\Spare\Providers\SpareServiceProvider;
use App\Modules\Trip\Providers\TripServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\ModuleServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    ModuleServiceProvider::class,
    AdminServiceProvider::class,
    ManagerServiceProvider::class,
    DriverServiceProvider::class,
    ClientServiceProvider::class,
    CommunicationServiceProvider::class,
    SpareServiceProvider::class,
    PaymentServiceProvider::class,
    TripServiceProvider::class,
    CashbookServiceProvider::class,
];
