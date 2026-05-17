<?php

declare(strict_types=1);

namespace App\Modules\Cashbook\Listeners;

use App\Modules\Cashbook\Enums\CashbookType;
use App\Modules\Cashbook\Services\CashbookService;
use App\Modules\Trip\Models\TripExpense;

class RecordExpenseInCashbook
{
    public function __construct(private readonly CashbookService $cashbookService) {}

    public function handle(TripExpense $expense): void
    {
        if ((bool) $expense->is_rejected) {
            return;
        }

        $description = $expense->description !== null && $expense->description !== ''
            ? $expense->description
            : 'Trip expense paid';

        $this->cashbookService->record([
            'reference_id' => $expense->ulid,
            'reference_type' => 'trip_expense',
            'type' => CashbookType::Debit,
            'amount' => (float) $expense->amount,
            'description' => $description,
            'entry_date' => $expense->expense_date,
            'recorded_by' => $expense->recorded_by,
            'note' => null,
        ]);
    }
}
