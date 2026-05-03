<?php

namespace App\Modules\Dashboard\Services;

use App\Modules\Dashboard\Repositories\DashboardRepositoryInterface;

class DashboardService
{
    public function __construct(protected DashboardRepositoryInterface $repository) {}
}
