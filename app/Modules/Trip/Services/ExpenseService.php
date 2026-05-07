<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

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
            ]);

            $trip->total_expense = (float) $trip->expenses()->sum('amount');
            $trip->save();

            $this->recalculateTripFinancials->execute($trip);

            return $expense;
        });
    }
}
