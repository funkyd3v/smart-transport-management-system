<?php

namespace App\Modules\Spare\Services;

use App\Modules\Spare\Repositories\SpareRepositoryInterface;

class SpareService
{
    public function __construct(protected SpareRepositoryInterface $repository) {}
}
