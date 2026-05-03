<?php

namespace App\Modules\Driver\Services;

use App\Modules\Driver\Repositories\DriverRepositoryInterface;

class DriverService
{
    public function __construct(protected DriverRepositoryInterface $repository) {}
}
