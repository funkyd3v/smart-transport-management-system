<?php

declare(strict_types=1);

namespace App\Modules\Admin\Providers;

use App\Models\User;
use App\Modules\Admin\Models\CompanySetting;
use App\Modules\Admin\Models\LoginHistory;
use App\Modules\Admin\Models\NotificationPreference;
use App\Modules\Admin\Repositories\AdminDashboardRepository;
use App\Modules\Admin\Repositories\AdminDashboardRepositoryInterface;
use App\Modules\Admin\Repositories\AdminOperationsRepository;
use App\Modules\Admin\Repositories\AdminOperationsRepositoryInterface;
use App\Modules\Admin\Repositories\ProfileRepository;
use App\Modules\Admin\Repositories\ProfileRepositoryInterface;
use App\Modules\Admin\Repositories\Settings\SettingRepository;
use App\Modules\Admin\Repositories\Settings\SettingRepositoryInterface;
use App\Modules\Admin\Repositories\Settings\UserManagementRepository;
use App\Modules\Admin\Repositories\Settings\UserManagementRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdminDashboardRepositoryInterface::class, AdminDashboardRepository::class);
        $this->app->bind(AdminOperationsRepositoryInterface::class, AdminOperationsRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->bind(UserManagementRepositoryInterface::class, UserManagementRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'admin');
        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components');

        Relation::enforceMorphMap([
            User::class => User::class,
            'company_setting' => CompanySetting::class,
            'notification_preference' => NotificationPreference::class,
            'login_history' => LoginHistory::class,
        ]);
    }
}
