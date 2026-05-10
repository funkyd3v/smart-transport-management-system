<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Driver\Http\Requests\Trip\RecordExpenseRequest;
use App\Modules\Driver\Models\Driver;
use App\Modules\Driver\Repositories\Trip\DriverTripRepositoryInterface;
use App\Modules\Driver\Services\DriverTripService;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\JsonResponse;

final class TripExpenseController extends Controller
{
    public function __construct(
        private readonly DriverTripService $driverTripService,
        private readonly DriverTripRepositoryInterface $tripRepository,
    ) {}

    public function store(RecordExpenseRequest $request, Trip $trip): JsonResponse
    {
        $driver = $this->resolveDriver($request->user());
        $ownedTrip = $this->tripRepository->findByIdForDriver((int) $trip->id, (int) $driver->id);
        $this->authorize('addExpense', $ownedTrip);

        $expense = $this->driverTripService->recordExpense($ownedTrip, $request->validated(), $request->user());
        $expense->load('category');
        $detail = $this->driverTripService->getTripDetail($ownedTrip);

        return response()->json([
            'message' => 'Expense recorded successfully.',
            'expense' => [
                'id' => $expense->id,
                'category' => (string) ($expense->category?->name ?? $request->validated()['category']),
                'amount' => (float) $expense->amount,
                'description' => (string) ($expense->description ?? ''),
                'expense_date' => optional($expense->expense_date)->format('Y-m-d'),
            ],
            'financial_summary' => $this->driverTripService->getFinancialSummary($detail),
        ]);
    }

    private function resolveDriver(?User $user): Driver
    {
        abort_unless($user instanceof User, 401);

        return Driver::query()->where('user_id', $user->id)->firstOrFail();
    }
}
