<?php

namespace App\Modules\Cashbook\Services;

use App\Modules\Cashbook\Enums\CashbookType;
use App\Modules\Cashbook\Models\DailyCashbook;
use App\Modules\Cashbook\Repositories\CashbookRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashbookService
{
    public function __construct(protected CashbookRepositoryInterface $repository) {}

    public function record(array $data): DailyCashbook
    {
        return DB::transaction(function () use ($data): DailyCashbook {
            $type = $data['type'] instanceof CashbookType
                ? $data['type']
                : CashbookType::from((string) $data['type']);

            $amount = (float) $data['amount'];
            $currentBalance = $this->repository->getLastBalance();
            $newBalance = $type === CashbookType::Credit
                ? $currentBalance + $amount
                : $currentBalance - $amount;

            return $this->repository->create([
                'reference_id' => $data['reference_id'] ?? null,
                'reference_type' => $data['reference_type'] ?? 'manual',
                'type' => $type,
                'amount' => $amount,
                'balance' => $newBalance,
                'description' => $data['description'],
                'entry_date' => $data['entry_date'],
                'recorded_by' => $data['recorded_by'] ?? Auth::id(),
                'note' => $data['note'] ?? null,
                'is_void' => false,
            ]);
        });
    }

    public function getBalance(): float
    {
        return $this->repository->getLastBalance();
    }

    public function getDailySummary(Carbon $date): array
    {
        return $this->repository->getDailySummary($date);
    }

    public function getMonthlySummary(Carbon $month): array
    {
        return $this->repository->getMonthlySummary($month);
    }

    public function getPaginatedEntries(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateEntries($filters);
    }

    public function findById(string $id): ?DailyCashbook
    {
        return $this->repository->findById($id);
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }
}
