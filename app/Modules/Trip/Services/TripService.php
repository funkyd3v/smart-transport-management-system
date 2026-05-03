<?php

namespace App\Modules\Trip\Services;

use App\Modules\Trip\Repositories\TripRepositoryInterface;

class TripService
{
    public function __construct(protected TripRepositoryInterface $repository) {}
}
