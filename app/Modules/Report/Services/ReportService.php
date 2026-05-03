<?php

namespace App\Modules\Report\Services;

use App\Modules\Report\Repositories\ReportRepositoryInterface;

class ReportService
{
    public function __construct(protected ReportRepositoryInterface $repository) {}
}
