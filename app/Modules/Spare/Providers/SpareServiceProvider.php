<?php

declare(strict_types=1);

namespace App\Modules\Spare\Providers;

use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Observers\SparePartObserver;
use App\Modules\Spare\Repositories\SpareRepository;
use App\Modules\Spare\Repositories\SpareRepositoryInterface;
use App\Modules\Spare\Repositories\SpareSaleRepository;
use App\Modules\Spare\Repositories\SpareSaleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class SpareServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SpareRepositoryInterface::class, SpareRepository::class);
        $this->app->bind(SpareSaleRepositoryInterface::class, SpareSaleRepository::class);
    }

    public function boot(): void
    {
        SparePart::observe(SparePartObserver::class);

        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }
}
