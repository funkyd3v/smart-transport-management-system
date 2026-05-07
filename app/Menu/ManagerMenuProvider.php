<?php

declare(strict_types=1);

namespace App\Menu;

use App\Contracts\MenuProviderInterface;
use Illuminate\Support\Facades\Route;

class ManagerMenuProvider implements MenuProviderInterface
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
                        'path' => $this->safeRoute('manager.dashboard'),
                    ],
                ],
            ],
            [
                'title' => 'Client Management',
                'items' => [
                    [
                        'name' => 'Clients',
                        'icon' => 'users',
                        'subItems' => [
                            [
                                'name' => 'Client List',
                                'path' => $this->safeRoute('manager.clients.index'),
                            ],
                            [
                                'name' => 'Add New Client',
                                'path' => $this->safeRoute('manager.clients.create'),
                            ],
                            [
                                'name' => 'Client Profile',
                                'path' => $this->safeRoute('manager.clients.show'),
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Driver Management',
                'items' => [
                    [
                        'name' => 'Drivers',
                        'icon' => 'truck',
                        'subItems' => [
                            [
                                'name' => 'Driver List',
                                'path' => $this->safeRoute('manager.drivers.index'),
                            ],
                            [
                                'name' => 'Add New Driver',
                                'path' => $this->safeRoute('manager.drivers.create'),
                            ],
                            [
                                'name' => 'Driver Profile',
                                'path' => $this->safeRoute('manager.drivers.show'),
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Truck Management',
                'items' => [
                    [
                        'name' => 'Trucks',
                        'icon' => 'truck',
                        'subItems' => [
                            [
                                'name' => 'Truck List',
                                'path' => $this->safeRoute('manager.trucks.index'),
                            ],
                            [
                                'name' => 'Add New Truck',
                                'path' => $this->safeRoute('manager.trucks.create'),
                            ],
                            [
                                'name' => 'Truck Profile',
                                'path' => $this->safeRoute('manager.trucks.show'),
                            ],
                        ],
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
