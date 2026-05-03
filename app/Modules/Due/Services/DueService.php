<?php

namespace App\Modules\Due\Services;

use App\Modules\Due\Repositories\DueRepositoryInterface;

class DueService
{
    public function __construct(protected DueRepositoryInterface $repository) {}
}
