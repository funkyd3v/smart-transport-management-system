<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Trip\Actions\RecordExpenseAction;
use App\Modules\Trip\DTOs\RecordExpenseDTO;
use App\Modules\Trip\Http\Requests\RecordExpenseRequest;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use App\Modules\Trip\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ExpenseController extends Controller
{
    public function __construct(
        private readonly RecordExpenseAction $recordExpense,
        private readonly ExpenseService $expenseService,
    ) {}

    public function create(string $tripUlid): View
    {
        $trip = Trip::query()->where('ulid', $tripUlid)->firstOrFail();
        $this->authorize('recordExpense', $trip);

        return view('trip::admin.expenses.create', compact('trip'));
    }

    public function store(RecordExpenseRequest $request): RedirectResponse
    {
        $trip = Trip::query()->where('ulid', $request->validated()['trip_ulid'])->firstOrFail();
        $this->authorize('recordExpense', $trip);

        $dto = RecordExpenseDTO::fromRequest($request);
        ($this->recordExpense)($dto);

        return redirect()->route('admin.trips.show', $trip->ulid)->with('success', 'Expense recorded successfully.');
    }

    public function approve(Request $request, string $tripUlid, TripExpense $expense): RedirectResponse
    {
        $trip = Trip::query()->where('ulid', $tripUlid)->firstOrFail();
        $this->authorize('recordExpense', $trip);

        abort_if((int) $expense->trip_id !== (int) $trip->id, 403);
        abort_if($expense->is_approved, 422, 'Expense already approved.');

        $this->expenseService->approve($expense, $request->user());

        return redirect()->route('admin.trips.show', $trip->ulid)->with('success', 'Expense approved.');
    }
}
