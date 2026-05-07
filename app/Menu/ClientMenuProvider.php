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
                ],
            ],
        ];
    }

    private function safeRoute(string $name): string
    {
        return Route::has($name) ? route($name) : '#';
    }
}
