<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach (File::directories(app_path('Modules')) as $modulePath) {
            $moduleName = basename($modulePath);
            $interface = "App\\Modules\\{$moduleName}\\Repositories\\{$moduleName}RepositoryInterface";
            $repository = "App\\Modules\\{$moduleName}\\Repositories\\{$moduleName}Repository";

            if (interface_exists($interface) && class_exists($repository)) {
                $this->app->bind($interface, $repository);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (File::directories(app_path('Modules')) as $modulePath) {
            $moduleName = basename($modulePath);
            $moduleProviderPath = $modulePath.'/Providers/'.$moduleName.'ServiceProvider.php';

            if (File::exists($moduleProviderPath)) {
                continue;
            }

            $webRoutesPath = $modulePath.'/Routes/web.php';
            $apiRoutesPath = $modulePath.'/Routes/api.php';

            if (File::exists($webRoutesPath)) {
                Route::middleware('web')->group($webRoutesPath);
            }

            if (File::exists($apiRoutesPath)) {
                Route::prefix('api')->middleware('api')->group($apiRoutesPath);
            }
        }
    }
}
