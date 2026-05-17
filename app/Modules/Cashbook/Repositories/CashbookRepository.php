<?php

namespace App\Modules\Cashbook\Repositories;

use App\Modules\Cashbook\Enums\CashbookType;
use App\Modules\Cashbook\Models\DailyCashbook;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CashbookRepository implements CashbookRepositoryInterface
{
    public function all(): Collection
    {
        return DailyCashbook::query()
            ->with('recordedBy:id,name')
            ->where('is_void', false)
            ->orderByDesc('entry_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function findById(string $id): ?DailyCashbook
    {
        return DailyCashbook::query()
            ->with('recordedBy:id,name')
            ->find($id);
    }

    public function create(array $data): DailyCashbook
    {
        return DailyCashbook::query()->create($data);
    }

    public function update(string $id, array $data): ?DailyCashbook
    {
        $entry = DailyCashbook::query()->find($id);

        if ($entry === null) {
            return null;
        }

        $entry->update($data);

        return $entry->fresh(['recordedBy:id,name']);
    }

    public function delete(string $id): bool
    {
        $entry = DailyCashbook::query()->find($id);

        if ($entry === null) {
            return false;
        }

        $entry->forceFill([
            'is_void' => true,
            'voided_at' => now(),
            'voided_by' => Auth::id(),
        ])->save();

        return true;
    }

    public function getLastBalance(): float
    {
        $lastEntry = DailyCashbook::query()
            ->where('is_void', false)
            ->orderByDesc('entry_date')
            ->orderByDesc('created_at')
            ->first();

        if ($lastEntry === null) {
            return 0.0;
        }

        return (float) $lastEntry->balance;
    }

    public function getDailySummary(Carbon $date): array
    {
        $baseQuery = DailyCashbook::query()
            ->whereDate('entry_date', $date->toDateString())
            ->where('is_void', false);

        $credits = (float) (clone $baseQuery)->where('type', CashbookType::Credit->value)->sum('amount');
        $debits = (float) (clone $baseQuery)->where('type', CashbookType::Debit->value)->sum('amount');

        return [
            'date' => $date->toDateString(),
            'total_credits' => $credits,
            'total_debits' => $debits,
            'net' => $credits - $debits,
        ];
    }

    public function getMonthlySummary(Carbon $month): array
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $baseQuery = DailyCashbook::query()
            ->whereBetween('entry_date', [$start, $end])
            ->where('is_void', false);

        $credits = (float) (clone $baseQuery)->where('type', CashbookType::Credit->value)->sum('amount');
        $debits = (float) (clone $baseQuery)->where('type', CashbookType::Debit->value)->sum('amount');

        return [
            'month' => $month->format('F Y'),
            'total_credits' => $credits,
            'total_debits' => $debits,
            'net' => $credits - $debits,
            'current_balance' => $this->getLastBalance(),
        ];
    }

    public function paginateEntries(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return DailyCashbook::query()
            ->with('recordedBy:id,name')
            ->where('is_void', false)
            ->forDateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null)
            ->when(
                isset($filters['type']) && in_array($filters['type'], [CashbookType::Credit->value, CashbookType::Debit->value], true),
                fn ($query) => $query->where('type', $filters['type'])
            )
            ->orderByDesc('entry_date')
            ->orderByDesc('created_at')
            ->paginate((int) ($filters['per_page'] ?? $perPage));
    }
}
