<?php

declare(strict_types=1);

namespace App\Modules\Manager\Providers;

use App\Modules\Manager\Repositories\Client\ClientRepository;
use App\Modules\Manager\Repositories\Client\ClientRepositoryInterface;
use App\Modules\Manager\Repositories\Driver\DriverRepository;
use App\Modules\Manager\Repositories\Driver\DriverRepositoryInterface;
use App\Modules\Manager\Repositories\Truck\TruckRepository;
use App\Modules\Manager\Repositories\Truck\TruckRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(DriverRepositoryInterface::class, DriverRepository::class);
        $this->app->bind(TruckRepositoryInterface::class, TruckRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'manager');
    }
}
