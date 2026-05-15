<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories;

use Illuminate\Support\Collection;

interface AdminDashboardRepositoryInterface
{
    public function totalTrucks(): int;

    public function runningTrips(): int;

    public function workshopTrucks(): int;

    public function todayIncome(): float;

    public function todayExpense(): float;

    public function todayDue(): float;

    public function monthlyProfit(): float;

    public function totalClients(): int;

    public function activeDrivers(): int;

    public function sparePartsValue(): float;

    public function revenueExpenseByMonth(int $months = 6): array;

    public function recentTrips(int $limit = 10): Collection;

    public function topOverdueClients(int $limit = 5): Collection;

    public function tripStatusBreakdown(): array;

    public function topDrivers(int $limit = 5): Collection;

    public function recentAuditLogs(int $limit = 5): Collection;

    public function pendingApprovalsCount(): int;

    public function pendingCompletionRequestsCount(): int;

    public function pendingExpenseApprovalsCount(): int;
}
