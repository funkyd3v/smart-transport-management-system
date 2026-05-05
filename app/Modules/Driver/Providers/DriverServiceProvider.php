<?php

declare(strict_types=1);

namespace App\Modules\Driver\Providers;

use Illuminate\Support\ServiceProvider;

class DriverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'driver');
    }
}
