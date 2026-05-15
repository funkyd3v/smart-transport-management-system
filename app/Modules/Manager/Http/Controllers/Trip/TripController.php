<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Modules\Client\Models\Client;
use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Manager\Http\Requests\Trip\StoreTripRequest;
use App\Modules\Manager\Http\Requests\Trip\UpdateTripRequest;
use App\Modules\Manager\Repositories\Driver\DriverRepositoryInterface;
use App\Modules\Manager\Repositories\Truck\TruckRepositoryInterface;
use App\Modules\Payment\Models\PaymentMethod;
use App\Modules\Trip\Actions\CreateTripAction;
use App\Modules\Trip\Actions\UpdateTripStatusAction;
use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;
use App\Modules\Truck\Models\Truck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TripController extends Controller
{
    public function __construct(
        private readonly CreateTripAction $createTrip,
        private readonly UpdateTripStatusAction $updateTripStatus,
        private readonly TripRepositoryInterface $tripRepository,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Trip::class);

        $filters = $request->only(['status_id', 'client_id', 'truck_id', 'date_from', 'date_to', 'search']);
        $trips = $this->tripRepository->paginate($filters);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('manager::trips.partials._table', [
                    'trips' => $trips,
                ])->render(),
            ]);
        }

        return view('manager::trips.index', [
            'trips' => $trips,
            'statuses' => TripStatus::query()->orderBy('name')->get(),
            'clients' => Client::query()->with('user')->orderBy('id')->get(),
            'trucks' => Truck::query()->orderBy('truck_number')->get(['id', 'truck_number']),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Trip::class);

        return view('manager::trips.create', [
            'clients' => Client::query()->with('user')->orderBy('id')->get(),
            'drivers' => app(DriverRepositoryInterface::class)->getAssignableDrivers(),
            'trucks' => app(TruckRepositoryInterface::class)->getAssignableTrucks(),
        ]);
    }

    public function store(StoreTripRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Trip::class);

        $data = $request->validated();
        $createdStatus = TripStatus::query()->firstOrCreate(['name' => 'created']);

        $dto = new CreateTripDTO(
            clientId: (int) $data['client_id'],
            truckId: (int) $data['truck_id'],
            driverId: (int) $data['driver_id'],
            createdBy: (int) $request->user()->id,
            statusId: (int) $createdStatus->id,
            pickupPoint: (string) $data['pickup_point'],
            deliveryPoint: (string) $data['delivery_point'],
            routeDescription: $data['route_description'] ?? null,
            goodsDescription: $data['goods_description'] ?? null,
            loadDate: (string) ($data['load_datetime'] ?? $data['load_date']),
            expectedDeliveryDate: $data['expected_delivery_date'] ?? null,
            tripRate: (float) $data['trip_rate'],
            advancePayment: (float) ($data['advance_payment'] ?? 0),
            notes: $data['notes'] ?? null,
            smsNote: $data['sms_note'] ?? null,
            goods: $data['goods'],
        );

        $trip = ($this->createTrip)($dto);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Trip created successfully.',
                'redirect' => route('manager.trips.show', $trip),
            ]);
        }

        return redirect()->route('manager.trips.show', $trip)->with('success', 'Trip created successfully.');
    }

    public function show(Trip $trip): View
    {
        $tripModel = $this->tripRepository->findByUlid($trip->ulid);
        $this->authorize('view', $tripModel);

        $tripModel->loadCount('goods')
            ->loadSum('goods', 'total_price')
            ->loadSum('expenses', 'amount')
            ->loadSum('payments', 'amount');

        $paymentsTotal = (float) ($tripModel->payments_sum_amount ?? 0);
        $advancePaid = (float) $tripModel->advance_payment;
        $totalPaid = $paymentsTotal + $advancePaid;
        $totalExpense = (float) ($tripModel->expenses_sum_amount ?? 0);

        $summary = [
            'trip_rate' => (float) $tripModel->trip_rate,
            'advance_paid' => $advancePaid,
            'payments_total' => $paymentsTotal,
            'total_paid' => $totalPaid,
            'total_expense' => $totalExpense,
            'due_balance' => max(0, (float) $tripModel->trip_rate - $totalPaid),
            'profit' => (float) $tripModel->trip_rate - $totalExpense,
        ];

        return view('manager::trips.show', [
            'trip' => $tripModel,
            'summary' => $summary,
            'expenseCategories' => ExpenseCategory::query()->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::query()->orderBy('name')->get(),
        ]);
    }

    public function updateStatus(UpdateTripRequest $request, Trip $trip): RedirectResponse|JsonResponse
    {
        $this->authorize('updateStatus', $trip);

        $validated = $request->validated();

        $dto = new UpdateTripStatusDTO(
            tripUlid: $trip->ulid,
            status: \App\Modules\Trip\Enums\TripStatus::from((string) $validated['status']),
            updatedBy: (int) $request->user()->id,
            note: $validated['note'] ?? null,
        );

        ($this->updateTripStatus)($dto);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Trip status updated successfully.',
            ]);
        }

        return back()->with('success', 'Trip status updated successfully.');
    }
}
