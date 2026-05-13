<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services;

final class AdminReportService
{
    public function reportTypes(): array
    {
        return [
            'daily-trip',
            'monthly-business',
            'income-expense',
            'client-due',
            'driver-performance',
            'spare-profit',
        ];
    }
}
