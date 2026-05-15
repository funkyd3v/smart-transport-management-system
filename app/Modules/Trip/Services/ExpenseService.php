<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Models\User;
use App\Modules\Trip\DTOs\RecordExpenseDTO;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ExpenseService
{
    public function __construct(
        private readonly RecalculateTripFinancials $recalculateTripFinancials,
    ) {}

    public function record(RecordExpenseDTO $dto): TripExpense
    {
        return DB::transaction(function () use ($dto): TripExpense {
            $trip = Trip::query()->where('ulid', $dto->tripUlid)->lockForUpdate()->firstOrFail();

            if (! $trip->canAddExpense()) {
                throw new RuntimeException('Expenses can only be added when trip is in transit or completed.');
            }

            $expense = TripExpense::query()->create([
                'ulid' => str()->ulid()->toBase32(),
                'trip_id' => $trip->id,
                'category_id' => $dto->categoryId,
                'recorded_by' => $dto->recordedBy,
                'amount' => $dto->amount,
                'description' => $dto->description,
                'expense_date' => $dto->expenseDate,
                'receipt_path' => $dto->receiptPath,
                'is_approved' => $dto->isApproved,
            ]);

            $trip->total_expense = (float) $trip->expenses()->where('is_approved', true)->sum('amount');
            $trip->save();

            $this->recalculateTripFinancials->execute($trip);

            return $expense;
        });
    }

    public function approve(TripExpense $expense, User $approvedBy): TripExpense
    {
        return DB::transaction(function () use ($expense, $approvedBy): TripExpense {
            $trip = Trip::query()->where('id', $expense->trip_id)->lockForUpdate()->firstOrFail();

            $expense->is_approved = true;
            $expense->is_rejected = false;
            $expense->approved_by = $approvedBy->id;
            $expense->approved_at = now();
            $expense->rejected_by = null;
            $expense->rejected_at = null;
            $expense->save();

            $trip->total_expense = (float) $trip->expenses()->where('is_approved', true)->sum('amount');
            $trip->save();

            $this->recalculateTripFinancials->execute($trip);

            return $expense;
        });
    }

    public function reject(TripExpense $expense, User $rejectedBy): TripExpense
    {
        return DB::transaction(function () use ($expense, $rejectedBy): TripExpense {
            $trip = Trip::query()->where('id', $expense->trip_id)->lockForUpdate()->firstOrFail();

            $expense->is_approved = false;
            $expense->is_rejected = true;
            $expense->approved_by = null;
            $expense->approved_at = null;
            $expense->rejected_by = $rejectedBy->id;
            $expense->rejected_at = now();
            $expense->save();

            $trip->total_expense = (float) $trip->expenses()->where('is_approved', true)->sum('amount');
            $trip->save();

            $this->recalculateTripFinancials->execute($trip);

            return $expense;
        });
    }
}
