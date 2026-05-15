<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use App\Modules\Trip\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class TripExpenseApprovalController extends Controller
{
    public function __construct(private readonly ExpenseService $expenseService) {}

    public function approve(Request $request, Trip $trip, TripExpense $expense): RedirectResponse|JsonResponse
    {
        $this->authorize('recordExpense', $trip);

        abort_if((int) $expense->trip_id !== (int) $trip->id, 403);
        abort_if($expense->is_approved, 422, 'Expense already approved.');
        abort_if($expense->is_rejected, 422, 'Rejected expense cannot be approved.');

        $expense = $this->expenseService->approve($expense, $request->user());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Expense approved successfully.',
                'expense' => [
                    'id' => $expense->id,
                    'is_approved' => true,
                    'approved_at' => $expense->approved_at?->format('Y-m-d H:i'),
                ],
            ]);
        }

        return redirect()->route('manager.trips.show', $trip->ulid)->with('success', 'Expense approved.');
    }

    public function reject(Request $request, Trip $trip, TripExpense $expense): RedirectResponse|JsonResponse
    {
        $this->authorize('recordExpense', $trip);

        abort_if((int) $expense->trip_id !== (int) $trip->id, 403);
        abort_if($expense->is_approved, 422, 'Approved expense cannot be rejected.');
        abort_if($expense->is_rejected, 422, 'Expense already rejected.');

        $expense = $this->expenseService->reject($expense, $request->user());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Expense rejected successfully.',
                'expense' => [
                    'id' => $expense->id,
                    'is_rejected' => true,
                    'rejected_at' => $expense->rejected_at?->format('Y-m-d H:i'),
                ],
            ]);
        }

        return redirect()->route('manager.trips.show', $trip->ulid)->with('success', 'Expense rejected.');
    }
}
