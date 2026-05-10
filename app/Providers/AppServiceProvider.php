<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\MenuProviderInterface;
use App\Menu\MenuResolver;
use App\Modules\Client\Models\Client;
use App\Modules\Manager\Policies\ClientPolicy;
use App\Modules\Trip\Models\Trip;
use App\Policies\TripPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MenuProviderInterface::class, function () {
            return MenuResolver::resolve((string) request()->segment(1));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Trip::class, TripPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
    }
}
