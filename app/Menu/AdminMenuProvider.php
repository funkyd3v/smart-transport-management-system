<?php

declare(strict_types=1);

namespace App\Menu;

use App\Contracts\MenuProviderInterface;
use Illuminate\Support\Facades\Route;

class AdminMenuProvider implements MenuProviderInterface
{
    public function getGroups(): array
    {
        return [
            [
                'title' => '',
                'items' => [
                    [
                        'name' => 'Dashboard',
                        'icon' => 'dashboard',
                        'path' => $this->safeRoute('admin.dashboard'),
                    ],
                    [
                        'name' => 'Users',
                        'icon' => 'users',
                        'path' => $this->safeRoute('admin.users.index'),
                    ],
                    [
                        'name' => 'Trips',
                        'icon' => 'truck',
                        'path' => $this->safeRoute('admin.trips.index'),
                    ],
                    [
                        'name' => 'Finance',
                        'icon' => 'chart',
                        'subItems' => [
                            [
                                'name' => 'Overview',
                                'path' => $this->safeRoute('admin.finance.index'),
                            ],
                            [
                                'name' => 'Dues',
                                'path' => $this->safeRoute('admin.finance.dues'),
                            ],
                            [
                                'name' => 'Cashbook',
                                'path' => $this->safeRoute('admin.finance.cashbook'),
                            ],
                        ],
                    ],
                    [
                        'name' => 'Drivers',
                        'icon' => 'table',
                        'path' => $this->safeRoute('admin.drivers.index'),
                    ],
                    [
                        'name' => 'Trucks',
                        'icon' => 'truck',
                        'path' => $this->safeRoute('admin.trucks.index'),
                    ],
                    [
                        'name' => 'Clients',
                        'icon' => 'users',
                        'path' => $this->safeRoute('admin.clients.index'),
                    ],
                    [
                        'name' => 'Spare Parts',
                        'icon' => 'table',
                        'subItems' => [
                            [
                                'name' => 'Inventory',
                                'path' => $this->safeRoute('admin.spare.inventory.index'),
                            ],
                            [
                                'name' => 'Sales',
                                'path' => $this->safeRoute('admin.spare.sales.index'),
                            ],
                        ],
                    ],
                    [
                        'name' => 'Reports',
                        'icon' => 'chart',
                        'path' => $this->safeRoute('admin.reports.index'),
                    ],
                    [
                        'name' => 'Audit Log',
                        'icon' => 'table',
                        'path' => $this->safeRoute('admin.audit.index'),
                    ],
                    [
                        'name' => 'Settings',
                        'icon' => 'settings',
                        'subItems' => [
                            [
                                'name' => 'General',
                                'path' => $this->safeRoute('admin.settings.general.index'),
                            ],
                            [
                                'name' => 'Financial',
                                'path' => $this->safeRoute('admin.settings.financial.index'),
                            ],
                            [
                                'name' => 'Notifications',
                                'path' => $this->safeRoute('admin.settings.notifications.index'),
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
