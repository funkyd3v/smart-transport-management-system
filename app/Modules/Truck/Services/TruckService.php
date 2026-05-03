<?php

namespace App\Modules\Truck\Services;

use App\Modules\Truck\Repositories\TruckRepositoryInterface;

class TruckService
{
    public function __construct(protected TruckRepositoryInterface $repository) {}
}
