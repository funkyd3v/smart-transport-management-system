<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services;

use App\Modules\Admin\Repositories\AdminDashboardRepositoryInterface;

final class AdminDashboardService
{
    public function __construct(private readonly AdminDashboardRepositoryInterface $repository) {}

    public function dashboardData(): array
    {
        return [
            'kpisTop' => [
                'total_trucks' => $this->repository->totalTrucks(),
                'running_trips' => $this->repository->runningTrips(),
                'workshop_trucks' => $this->repository->workshopTrucks(),
                'today_income' => $this->repository->todayIncome(),
                'today_expense' => $this->repository->todayExpense(),
                'today_due' => $this->repository->todayDue(),
            ],
            'kpisSecond' => [
                'monthly_profit' => $this->repository->monthlyProfit(),
                'total_clients' => $this->repository->totalClients(),
                'active_drivers' => $this->repository->activeDrivers(),
                'spare_parts_value' => $this->repository->sparePartsValue(),
            ],
            'monthlyFinancials' => $this->repository->revenueExpenseByMonth(6),
            'recentTrips' => $this->repository->recentTrips(10),
            'topOverdueClients' => $this->repository->topOverdueClients(5),
            'tripStatusBreakdown' => $this->repository->tripStatusBreakdown(),
            'topDrivers' => $this->repository->topDrivers(5),
            'recentAuditLogs' => $this->repository->recentAuditLogs(5),
            'pendingApprovals' => $this->repository->pendingApprovalsCount(),
        ];
    }
}
