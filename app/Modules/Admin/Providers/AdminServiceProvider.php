<?php

declare(strict_types=1);

namespace App\Modules\Admin\Providers;

use App\Modules\Admin\Repositories\AdminDashboardRepository;
use App\Modules\Admin\Repositories\AdminDashboardRepositoryInterface;
use App\Modules\Admin\Repositories\AdminOperationsRepository;
use App\Modules\Admin\Repositories\AdminOperationsRepositoryInterface;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdminDashboardRepositoryInterface::class, AdminDashboardRepository::class);
        $this->app->bind(AdminOperationsRepositoryInterface::class, AdminOperationsRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'admin');
        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components');
    }
}
