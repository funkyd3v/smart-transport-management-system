<?php

declare(strict_types=1);

namespace App\Modules\Driver\Services;

use App\Models\User;
use App\Modules\Driver\Models\Driver;
use App\Modules\Driver\Repositories\Trip\DriverTripRepositoryInterface;
use App\Modules\Trip\Actions\AddReloadHistoryAction;
use App\Modules\Trip\Actions\CompleteTripAction;
use App\Modules\Trip\Actions\RecordExpenseAction;
use App\Modules\Trip\Actions\UpdateTripStatusAction;
use App\Modules\Trip\DTOs\RecordExpenseDTO;
use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\ReloadHistory;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripExpense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DriverTripService
{
    public function __construct(
        private readonly DriverTripRepositoryInterface $tripRepository,
        private readonly UpdateTripStatusAction $updateTripStatus,
        private readonly CompleteTripAction $completeTrip,
        private readonly RecordExpenseAction $recordExpense,
        private readonly AddReloadHistoryAction $addReloadHistory,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getDriverTrips(Driver $driver, array $filters): LengthAwarePaginator
    {
        return $this->tripRepository->getByDriver($driver->id, $filters, 15);
    }

    public function getTripDetail(Trip $trip): Trip
    {
        return $this->tripRepository->findWithFullDetail((int) $trip->id, (int) $trip->driver_id);
    }

    public function startTrip(Trip $trip): Trip
    {
        $updatedBy = (int) (request()->user()?->id ?? 0);

        $dto = new UpdateTripStatusDTO(
            tripUlid: $trip->ulid,
            status: TripStatus::InProgress,
            updatedBy: $updatedBy,
            note: null,
        );

        return ($this->updateTripStatus)($dto);
    }

    public function completeTrip(Trip $trip): Trip
    {
        $updatedBy = (int) (request()->user()?->id ?? 0);

        $dto = new UpdateTripStatusDTO(
            tripUlid: $trip->ulid,
            status: TripStatus::Completed,
            updatedBy: $updatedBy,
            note: null,
        );

        return ($this->completeTrip)($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function recordExpense(Trip $trip, array $data, User $recordedBy): TripExpense
    {
        $dto = new RecordExpenseDTO(
            tripUlid: $trip->ulid,
            categoryId: $this->tripRepository->resolveExpenseCategoryId((string) $data['category']),
            recordedBy: (int) $recordedBy->id,
            amount: (float) $data['amount'],
            description: $data['description'] ?? null,
            expenseDate: (string) $data['expense_date'],
            receiptPath: null,
        );

        return ($this->recordExpense)($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function addReload(Trip $trip, array $data): ReloadHistory
    {
        return ($this->addReloadHistory)(
            $trip,
            (int) $trip->truck_id,
            (int) $trip->driver_id,
            (string) $data['location'],
            $data['note'] ?? null,
            (float) $data['reload_amount'],
            (string) $data['reloaded_at'],
        );
    }

    /**
     * @return array{trip_rate: float, advance_received: float, total_expenses: float, net: float}
     */
    public function getFinancialSummary(Trip $trip): array
    {
        $tripRate = (float) $trip->trip_rate;
        $advanceReceived = (float) $trip->advance_payment;
        $totalExpenses = (float) ($trip->expenses_sum_amount ?? $trip->expenses->sum('amount'));

        return [
            'trip_rate' => $tripRate,
            'advance_received' => $advanceReceived,
            'total_expenses' => $totalExpenses,
            'net' => $tripRate - $totalExpenses,
        ];
    }
}
