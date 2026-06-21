<?php

declare(strict_types=1);

namespace App\Menu;

use App\Contracts\MenuProviderInterface;
use Illuminate\Support\Facades\Route;

class ClientMenuProvider implements MenuProviderInterface
{
    public function getGroups(): array
    {
        return [
            [
                'title' => 'Main Menu',
                'items' => [
                    [
                        'name' => 'Dashboard',
                        'icon' => 'dashboard',
                        'path' => $this->safeRoute('client.dashboard'),
                    ],
                    [
                        'name' => 'Payments',
                        'icon' => 'dollarLine',
                        'path' => $this->safeRoute('client.payments.index'),
                    ],
                ],
            ],
        ];
    }

    private function safeRoute(string $name): string
    {
        return Route::has($name) ? route($name) : '#';
    }
}
