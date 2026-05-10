<?php

declare(strict_types=1);

namespace App\Modules\Driver\Providers;

use App\Modules\Driver\Repositories\Trip\DriverTripRepository;
use App\Modules\Driver\Repositories\Trip\DriverTripRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class DriverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DriverTripRepositoryInterface::class, DriverTripRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'driver');
    }
}
