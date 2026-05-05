<?php

declare(strict_types=1);

use App\Modules\Admin\Providers\AdminServiceProvider;
use App\Modules\Client\Providers\ClientServiceProvider;
use App\Modules\Driver\Providers\DriverServiceProvider;
use App\Modules\Manager\Providers\ManagerServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\ModuleServiceProvider;

return [
    AppServiceProvider::class,
    ModuleServiceProvider::class,
    AdminServiceProvider::class,
    ManagerServiceProvider::class,
    DriverServiceProvider::class,
    ClientServiceProvider::class,
];
