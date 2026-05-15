<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Truck;

use App\Http\Controllers\Controller;
use App\Modules\Manager\Http\Requests\Truck\StoreTruckRequest;
use App\Modules\Manager\Http\Requests\Truck\UpdateTruckRequest;
use App\Modules\Manager\Services\TruckService;
use App\Modules\Truck\Models\Truck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TruckController extends Controller
{
    public function __construct(private readonly TruckService $truckService)
    {
        $this->authorizeResource(Truck::class, 'truck');
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'truck_type', 'status']);
        $trucks = $this->truckService->getFilteredPaginated($filters);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('manager::pages.trucks.partials._table', [
                    'trucks' => $trucks,
                ])->render(),
            ]);
        }

        return view('manager::pages.trucks.index', [
            'trucks' => $trucks,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('manager::pages.trucks.create');
    }

    public function store(StoreTruckRequest $request): JsonResponse
    {
        try {
            $this->truckService->create($request->validated() + ['created_by' => (int) $request->user()->id]);
        } catch (HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Truck created successfully.',
        ]);
    }

    public function show(Truck $truck): View
    {
        $truckWithStats = $this->truckService->getTruckWithStats($truck);

        return view('manager::pages.trucks.show', [
            'truck' => $truckWithStats,
            'stats' => $this->truckService->getStats($truckWithStats),
        ]);
    }

    public function edit(Truck $truck): View
    {
        return view('manager::pages.trucks.edit', [
            'truck' => $truck,
        ]);
    }

    public function update(UpdateTruckRequest $request, Truck $truck): JsonResponse
    {
        try {
            $this->truckService->update($truck, $request->validated());
        } catch (HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Truck updated successfully.',
        ]);
    }

    public function destroy(Truck $truck): JsonResponse
    {
        try {
            $this->truckService->delete($truck);
        } catch (HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Truck deleted successfully.',
        ]);
    }

    public function updateStatus(Request $request, Truck $truck): JsonResponse
    {
        $this->authorize('updateStatus', $truck);

        $validated = $request->validate([
            'status' => ['required', 'in:idle,under_workshop'],
        ]);

        try {
            $this->truckService->updateStatus($truck, (string) $validated['status']);
        } catch (HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Truck status updated successfully.',
            'status' => (string) $validated['status'],
        ]);
    }
}
