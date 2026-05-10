<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Driver\Policies\TripPolicy as DriverTripPolicy;
use App\Modules\Manager\Policies\ClientPolicy;
use App\Modules\Manager\Policies\DriverPolicy;
use App\Modules\Manager\Policies\TruckPolicy;
use App\Modules\Trip\Models\Trip;
use App\Modules\Truck\Models\Truck;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Trip::class, DriverTripPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Driver::class, DriverPolicy::class);
        Gate::policy(Truck::class, TruckPolicy::class);
    }
}
