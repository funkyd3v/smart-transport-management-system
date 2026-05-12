<?php

declare(strict_types=1);

namespace App\Modules\Manager\Services;

use App\Modules\Driver\Models\Driver;
use App\Modules\Due\Models\DueRecord;
use App\Modules\Payment\Models\Payment;
use App\Modules\Spare\Models\SpareCategory;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class DashboardService
{
    public function getTotalTrucks(): int
    {
        return Truck::query()->count();
    }

    public function getRunningTripsCount(): int
    {
        $runningStatusIds = $this->tripStatusIds([
            'running',
            'in_progress',
            'in transit',
            'in_transit',
            'active',
            'reload',
            'reloading',
        ]);

        return Trip::query()->whereIn('status_id', $runningStatusIds)->count();
    }

    public function getWorkshopTrucksCount(): int
    {
        $workshopStatusIds = $this->truckStatusIds([
            'under_workshop',
            'under workshop',
            'under maintenance',
            'under_maintenance',
            'workshop',
        ]);

        return Truck::query()->whereIn('status_id', $workshopStatusIds)->count();
    }

    public function getTodayIncome(): float
    {
        return (float) Payment::query()
            ->whereDate('created_at', today())
            ->sum('amount');
    }

    public function getTodayExpense(): float
    {
        return (float) TripExpense::query()
            ->whereDate('created_at', today())
            ->sum('amount');
    }

    public function getTodayDue(): float
    {
        return (float) DueRecord::query()
            ->whereDate('created_at', today())
            ->sum('remaining_due');
    }

    public function getMonthlyProfit(): float
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

    public function getActiveTrips(): Collection
    {
        $activeStatusIds = $this->tripStatusIds([
            'running',
            'reload',
            'reloading',
            'in_progress',
            'in transit',
            'in_transit',
            'active',
        ]);

        return Trip::query()
            ->with(['client.user', 'truck', 'driver.user', 'status'])
            ->whereIn('status_id', $activeStatusIds)
            ->latest('id')
            ->limit(8)
            ->get();
    }

    public function getTopDueClients(int $limit = 5): Collection
    {
        return DueRecord::query()
            ->selectRaw('client_id, SUM(remaining_due) as due_amount, MAX(updated_at) as last_payment_date')
            ->where('remaining_due', '>', 0)
            ->groupBy('client_id')
            ->with(['client.user'])
            ->orderByDesc('due_amount')
            ->limit($limit)
            ->get()
            ->map(function (DueRecord $record): array {
                $client = $record->client;

                return [
                    'client_id' => $record->client_id,
                    'name' => $client?->company_name ?? $client?->user?->name ?? ('Client #'.$record->client_id),
                    'due_amount' => (float) ($record->due_amount ?? 0),
                    'last_payment_date' => $record->last_payment_date,
                ];
            });
    }

    public function getTotalOutstandingDue(): float
    {
        return (float) DueRecord::query()
            ->where('is_settled', false)
            ->sum('remaining_due');
    }

    public function getMonthlyFinancials(int $months = 6): array
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

        $financials = [];

        for ($i = 0; $i < $months; $i++) {
            $monthDate = $startMonth->copy()->addMonths($i);
            $key = $monthDate->format('Y-m');

            $financials[] = [
                'month' => $monthDate->format('M'),
                'income' => (float) ($incomeByMonth[$key] ?? 0),
                'expense' => (float) ($expenseByMonth[$key] ?? 0),
            ];
        }

        return $financials;
    }

    public function getPaymentMethodBreakdown(): array
    {
        $methodSums = Payment::query()
            ->selectRaw('payment_methods.name as method_name, SUM(payments.amount) as total_amount')
            ->join('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->whereMonth('payments.payment_date', now()->month)
            ->whereYear('payments.payment_date', now()->year)
            ->groupBy('payment_methods.name')
            ->pluck('total_amount', 'method_name');

        $supportedMethods = ['bKash', 'Nagad', 'Cash', 'Bank Transfer'];
        $normalizedAmounts = [];

        foreach ($supportedMethods as $method) {
            $amount = 0.0;

            foreach ($methodSums as $methodName => $totalAmount) {
                if (strcasecmp((string) $methodName, $method) === 0) {
                    $amount = (float) $totalAmount;
                    break;
                }
            }

            $normalizedAmounts[$method] = $amount;
        }

        $grandTotal = array_sum($normalizedAmounts);

        $breakdown = [];
        foreach ($normalizedAmounts as $method => $amount) {
            $breakdown[] = [
                'method' => $method,
                'amount' => (float) $amount,
                'percentage' => $grandTotal > 0 ? round(($amount / $grandTotal) * 100, 2) : 0.0,
            ];
        }

        return $breakdown;
    }

    public function getDriverPerformance(int $limit = 8): Collection
    {
        $periodStart = now()->copy()->startOfMonth();
        $periodEnd = now()->copy()->endOfMonth();

        return Driver::query()
            ->with('user')
            ->withCount([
                'trips as trips_completed' => function ($query) use ($periodStart, $periodEnd): void {
                    $query->whereBetween('created_at', [$periodStart, $periodEnd]);
                },
            ])
            ->withSum([
                'trips as total_income_generated' => function ($query) use ($periodStart, $periodEnd): void {
                    $query->whereBetween('created_at', [$periodStart, $periodEnd]);
                },
            ], 'total_income')
            ->withSum([
                'trips as total_expenses' => function ($query) use ($periodStart, $periodEnd): void {
                    $query->whereBetween('created_at', [$periodStart, $periodEnd]);
                },
            ], 'total_expense')
            ->orderByDesc('total_income_generated')
            ->limit($limit)
            ->get()
            ->map(function (Driver $driver): array {
                $income = (float) ($driver->total_income_generated ?? 0);
                $expenses = (float) ($driver->total_expenses ?? 0);

                return [
                    'driver_id' => $driver->id,
                    'name' => $driver->name ?? $driver->user?->name ?? 'N/A',
                    'avatar' => $driver->avatar_url,
                    'trips' => (int) ($driver->trips_completed ?? 0),
                    'income' => $income,
                    'expenses' => $expenses,
                    'profit' => $income - $expenses,
                    'rating' => (float) ($driver->rating ?? 0),
                    'status' => (string) ($driver->status ?? 'inactive'),
                ];
            });
    }

    public function getSpareInventorySummary(): array
    {
        $lowStockThreshold = 10;

        return SpareCategory::query()
            ->withSum('spareParts as stock_count', 'quantity_in_stock')
            ->orderBy('name')
            ->get()
            ->map(function (SpareCategory $category) use ($lowStockThreshold): array {
                $stockCount = (int) ($category->stock_count ?? 0);

                return [
                    'category' => (string) $category->name,
                    'stock_count' => $stockCount,
                    'is_low_stock' => $stockCount < $lowStockThreshold,
                ];
            })
            ->all();
    }

    public function getTotalSpareParts(): int
    {
        return (int) SparePart::query()->sum('quantity_in_stock');
    }

    public function getRecentCashbook(int $limit = 7): Collection
    {
        $incomeEntries = Payment::query()
            ->with(['client.user'])
            ->latest('payment_date')
            ->limit($limit * 2)
            ->get()
            ->map(function (Payment $payment): array {
                $clientName = $payment->client?->company_name ?? $payment->client?->user?->name ?? 'Unknown Client';

                return [
                    'date' => $payment->payment_date,
                    'description' => 'Payment received from '.$clientName,
                    'type' => 'income',
                    'amount' => (float) $payment->amount,
                ];
            });

        $expenseEntries = TripExpense::query()
            ->with('trip')
            ->latest('expense_date')
            ->limit($limit * 2)
            ->get()
            ->map(function (TripExpense $expense): array {
                return [
                    'date' => $expense->expense_date,
                    'description' => $expense->description ?: 'Trip expense for '.($expense->trip?->trip_code ?? 'N/A'),
                    'type' => 'expense',
                    'amount' => (float) $expense->amount,
                ];
            });

        return $incomeEntries
            ->toBase()
            ->merge($expenseEntries->toBase())
            ->sortByDesc(function (array $entry): int {
                return Carbon::parse((string) $entry['date'])->timestamp;
            })
            ->take($limit)
            ->values();
    }

    /**
     * @param  array<int, string>  $statusNames
     * @return array<int, int>
     */
    private function tripStatusIds(array $statusNames): array
    {
        $normalizedNames = array_map(static fn (string $name): string => strtolower(trim($name)), $statusNames);

        $ids = TripStatus::query()
            ->whereIn('name', $statusNames)
            ->orWhereIn('name', $normalizedNames)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        if ($ids === []) {
            return [0];
        }

        return $ids;
    }

    /**
     * @param  array<int, string>  $statusNames
     * @return array<int, int>
     */
    private function truckStatusIds(array $statusNames): array
    {
        $normalizedNames = array_map(static fn (string $name): string => strtolower(trim($name)), $statusNames);

        $ids = TruckStatus::query()
            ->whereIn('name', $statusNames)
            ->orWhereIn('name', $normalizedNames)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        if ($ids === []) {
            return [0];
        }

        return $ids;
    }
}
