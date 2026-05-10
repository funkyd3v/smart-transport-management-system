<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Trip\Actions\RecordExpenseAction;
use App\Modules\Trip\DTOs\RecordExpenseDTO;
use App\Modules\Trip\Http\Requests\RecordExpenseRequest;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class TripExpenseController extends Controller
{
    public function __construct(private readonly RecordExpenseAction $recordExpense) {}

    public function create(string $tripUlid): View
    {
        $trip = Trip::query()->where('ulid', $tripUlid)->firstOrFail();
        $this->authorize('update', $trip);

        return view('manager::trips.expenses.create', [
            'trip' => $trip,
            'expenseCategories' => ExpenseCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(RecordExpenseRequest $request): RedirectResponse
    {
        $trip = Trip::query()->where('ulid', $request->validated()['trip_ulid'])->firstOrFail();
        $this->authorize('update', $trip);

        $dto = RecordExpenseDTO::fromRequest($request);
        ($this->recordExpense)($dto);

        return redirect()->route('manager.trips.show', $trip->ulid)->with('success', 'Expense recorded successfully.');
    }
}
