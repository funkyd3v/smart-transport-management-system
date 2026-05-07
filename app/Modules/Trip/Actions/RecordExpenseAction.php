<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\RecordExpenseDTO;
use App\Modules\Trip\Models\TripExpense;
use App\Modules\Trip\Services\ExpenseService;

class RecordExpenseAction
{
    public function __construct(private readonly ExpenseService $expenseService) {}

    public function __invoke(RecordExpenseDTO $dto): TripExpense
    {
        return $this->expenseService->record($dto);
    }
}
