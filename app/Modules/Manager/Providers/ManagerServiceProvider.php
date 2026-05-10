<?php

declare(strict_types=1);

namespace App\Modules\Manager\Providers;

use App\Modules\Manager\Repositories\Client\ClientRepository;
use App\Modules\Manager\Repositories\Client\ClientRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'manager');
    }
}
