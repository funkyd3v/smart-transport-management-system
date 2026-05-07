<?php

declare(strict_types=1);

namespace App\Menu;

use App\Contracts\MenuProviderInterface;

class MenuResolver
{
    public static function resolve(string $module): MenuProviderInterface
    {
        $providerClass = match ($module) {
            'manager' => ManagerMenuProvider::class,
            'driver' => DriverMenuProvider::class,
            'client' => ClientMenuProvider::class,
            'admin' => AdminMenuProvider::class,
            default => AdminMenuProvider::class,
        };

        return app($providerClass);
    }
}
