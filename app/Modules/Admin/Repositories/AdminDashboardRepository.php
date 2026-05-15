<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories;

use App\Models\User;
use App\Modules\AuditLog\Models\AuditLog;
use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Due\Models\DueRecord;
use App\Modules\Payment\Models\Payment;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Support\Collection;

final class AdminDashboardRepository implements AdminDashboardRepositoryInterface
{
    public function totalTrucks(): int
    {
        return Truck::query()->count();
    }

    public function runningTrips(): int
    {
        $runningStatusIds = $this->tripStatusIds(['in_progress', 'running', 'in transit', 'in_transit', 'active']);

        return Trip::query()->whereIn('status_id', $runningStatusIds)->count();
    }

    public function workshopTrucks(): int
    {
        $workshopStatusIds = $this->truckStatusIds(['workshop', 'under_workshop', 'under workshop', 'under maintenance']);

        return Truck::query()->whereIn('status_id', $workshopStatusIds)->count();
    }

    public function todayIncome(): float
    {
        return (float) Payment::query()->whereDate('payment_date', today())->sum('amount');
    }

    public function todayExpense(): float
    {
        return (float) TripExpense::query()->whereDate('expense_date', today())->sum('amount');
    }

    public function todayDue(): float
    {
        return (float) DueRecord::query()->where('is_settled', false)->sum('remaining_due');
    }

    public function monthlyProfit(): float
    {
        $now = now();

        $income = (float) Payment::query()
            ->whereMonth('payment_date', $now->month)
            ->whereYear('payment_date', $now->year)
            ->sum('amount');

        $expense = (float) TripExpense::query()
            ->whereMonth('expense_date', $now->month)
            ->whereYear('expense_date', $now->year)
            ->sum('amount');

        return $income - $expense;
    }

    public function totalClients(): int
    {
        return Client::query()->count();
    }

    public function activeDrivers(): int
    {
        return Driver::query()->where('is_available', true)->count();
    }

    public function sparePartsValue(): float
    {
        return (float) SparePart::query()->selectRaw('SUM(quantity_in_stock * purchase_price) as total')->value('total');
    }

    public function revenueExpenseByMonth(int $months = 6): array
    {
        $endMonth = now()->copy()->startOfMonth();
        $startMonth = $endMonth->copy()->subMonths(max(0, $months - 1));

        $incomeByMonth = Payment::query()
            ->selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total_amount')
            ->whereBetween('payment_date', [$startMonth->toDateString(), $endMonth->copy()->endOfMonth()->toDateString()])
            ->groupByRaw('YEAR(payment_date), MONTH(payment_date)')
            ->get()
            ->mapWithKeys(fn ($row): array => [sprintf('%04d-%02d', (int) $row->year, (int) $row->month) => (float) $row->total_amount]);

        $expenseByMonth = TripExpense::query()
            ->selectRaw('YEAR(expense_date) as year, MONTH(expense_date) as month, SUM(amount) as total_amount')
            ->whereBetween('expense_date', [$startMonth->toDateString(), $endMonth->copy()->endOfMonth()->toDateString()])
            ->groupByRaw('YEAR(expense_date), MONTH(expense_date)')
            ->get()
            ->mapWithKeys(fn ($row): array => [sprintf('%04d-%02d', (int) $row->year, (int) $row->month) => (float) $row->total_amount]);

        $rows = [];
        for ($i = 0; $i < $months; $i++) {
            $monthDate = $startMonth->copy()->addMonths($i);
            $key = $monthDate->format('Y-m');

            $rows[] = [
                'month' => $monthDate->format('M'),
                'income' => (float) ($incomeByMonth[$key] ?? 0),
                'expense' => (float) ($expenseByMonth[$key] ?? 0),
            ];
        }

        return $rows;
    }

    public function recentTrips(int $limit = 10): Collection
    {
        return Trip::query()
            ->with(['client.user:id,name', 'client:id,user_id,company_name', 'driver.user:id,name', 'driver:id,user_id', 'truck:id,truck_number', 'status:id,name'])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function topOverdueClients(int $limit = 5): Collection
    {
        return DueRecord::query()
            ->selectRaw('client_id, SUM(remaining_due) as due_amount, MAX(updated_at) as last_payment_date')
            ->where('remaining_due', '>', 0)
            ->groupBy('client_id')
            ->with(['client.user:id,name', 'client:id,user_id,company_name'])
            ->orderByDesc('due_amount')
            ->limit($limit)
            ->get();
    }

    public function tripStatusBreakdown(): array
    {
        $rows = TripStatus::query()
            ->withCount('trips')
            ->get()
            ->mapWithKeys(fn (TripStatus $status): array => [strtolower((string) $status->name) => (int) $status->trips_count])
            ->all();

        return [
            'pending' => (int) ($rows['pending'] ?? 0),
            'in_progress' => (int) ($rows['in_progress'] ?? 0),
            'completed' => (int) ($rows['completed'] ?? 0),
            'cancelled' => (int) ($rows['cancelled'] ?? 0),
        ];
    }

    public function topDrivers(int $limit = 5): Collection
    {
        return Driver::query()
            ->with('user:id,name')
            ->withCount('trips')
            ->orderByDesc('trips_count')
            ->limit($limit)
            ->get();
    }

    public function recentAuditLogs(int $limit = 5): Collection
    {
        return AuditLog::query()
            ->with('user:id,name,role')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function pendingApprovalsCount(): int
    {
        return User::query()
            ->where('is_active', false)
            ->count();
    }

    public function pendingCompletionRequestsCount(): int
    {
        return Trip::query()
            ->whereNotNull('completion_requested_at')
            ->whereNull('completed_at')
            ->count();
    }

    public function pendingExpenseApprovalsCount(): int
    {
        return TripExpense::query()->where('is_approved', false)->count();
    }

    private function tripStatusIds(array $names): array
    {
        return TripStatus::query()
            ->whereIn('name', $names)
            ->pluck('id')
            ->all();
    }

    private function truckStatusIds(array $names): array
    {
        return TruckStatus::query()
            ->whereIn('name', $names)
            ->pluck('id')
            ->all();
    }
}
