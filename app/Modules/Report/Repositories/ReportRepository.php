<?php

namespace App\Modules\Report\Repositories;

use App\Modules\Payment\Models\Payment;
use App\Modules\Spare\Models\SpareSale;
use App\Modules\Trip\Models\TripExpense;

class ReportRepository implements ReportRepositoryInterface
{
    public function summary(): array
    {
        return [
            'daily' => $this->dailyProfitBreakdown(),
            'monthly' => $this->monthlyProfitBreakdown(),
        ];
    }

    public function dailyProfitBreakdown(?string $date = null): array
    {
        $targetDate = $date ?? now()->toDateString();

        $tripIncome = (float) Payment::query()->whereDate('payment_date', $targetDate)->sum('amount');
        $spareSalesRevenue = (float) SpareSale::query()->whereDate('sold_at', $targetDate)->sum('sale_price');
        $tripExpenses = (float) TripExpense::query()->whereDate('expense_date', $targetDate)->sum('amount');
        $spareProfit = (float) SpareSale::query()->whereDate('sold_at', $targetDate)->sum('profit');
        $tripProfit = $tripIncome - $tripExpenses;

        return [
            'date' => $targetDate,
            'trip_income' => $tripIncome,
            'spare_sales_revenue' => $spareSalesRevenue,
            'total_income' => $tripIncome + $spareSalesRevenue,
            'trip_expenses' => $tripExpenses,
            'spare_related_expenses' => 0.0,
            'trip_profit' => $tripProfit,
            'spare_profit' => $spareProfit,
            'total_profit' => $tripProfit + $spareProfit,
        ];
    }

    public function monthlyProfitBreakdown(?int $year = null, ?int $month = null): array
    {
        $targetYear = $year ?? (int) now()->year;
        $targetMonth = $month ?? (int) now()->month;

        $tripIncome = (float) Payment::query()
            ->whereYear('payment_date', $targetYear)
            ->whereMonth('payment_date', $targetMonth)
            ->sum('amount');

        $spareSalesRevenue = (float) SpareSale::query()
            ->whereYear('sold_at', $targetYear)
            ->whereMonth('sold_at', $targetMonth)
            ->sum('sale_price');

        $tripExpenses = (float) TripExpense::query()
            ->whereYear('expense_date', $targetYear)
            ->whereMonth('expense_date', $targetMonth)
            ->sum('amount');

        $spareProfit = (float) SpareSale::query()
            ->whereYear('sold_at', $targetYear)
            ->whereMonth('sold_at', $targetMonth)
            ->sum('profit');

        $tripProfit = $tripIncome - $tripExpenses;

        return [
            'year' => $targetYear,
            'month' => $targetMonth,
            'trip_income' => $tripIncome,
            'spare_sales_revenue' => $spareSalesRevenue,
            'total_income' => $tripIncome + $spareSalesRevenue,
            'trip_expenses' => $tripExpenses,
            'spare_related_expenses' => 0.0,
            'trip_profit' => $tripProfit,
            'spare_profit' => $spareProfit,
            'total_profit' => $tripProfit + $spareProfit,
        ];
    }

    public function all() {}

    public function findByUlid(string $ulid) {}

    public function create(array $data) {}

    public function update(string $ulid, array $data) {}

    public function delete(string $ulid) {}
}
