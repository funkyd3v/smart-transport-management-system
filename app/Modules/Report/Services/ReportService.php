<?php

namespace App\Modules\Report\Services;

use App\Modules\Report\Repositories\ReportRepositoryInterface;

class ReportService
{
    public function __construct(protected ReportRepositoryInterface $repository) {}

    public function summary(): array
    {
        return $this->repository->summary();
    }

    public function dailyProfitBreakdown(?string $date = null): array
    {
        return $this->repository->dailyProfitBreakdown($date);
    }

    public function monthlyProfitBreakdown(?int $year = null, ?int $month = null): array
    {
        return $this->repository->monthlyProfitBreakdown($year, $month);
    }
}
