<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Modules\Driver\Models\Driver;
use App\Modules\Manager\Http\Requests\Driver\StoreDriverRequest;
use App\Modules\Manager\Http\Requests\Driver\UpdateDriverRequest;
use App\Modules\Manager\Services\DriverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DriverController extends Controller
{
    public function __construct(private readonly DriverService $driverService)
    {
        $this->authorizeResource(Driver::class, 'driver');
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'driving_type', 'status', 'is_approved']);
        $drivers = $this->driverService->getFilteredPaginated($filters);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('manager::pages.drivers.partials._table_with_pagination', [
                    'drivers' => $drivers,
                ])->render(),
            ]);
        }

        return view('manager::pages.drivers.index', [
            'drivers' => $drivers,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('manager::pages.drivers.create');
    }

    public function store(StoreDriverRequest $request): JsonResponse
    {
        $driver = $this->driverService->create($request);

        return response()->json([
            'message' => 'Driver created successfully.',
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
            ],
            'credentials' => [
                'email' => $request->validated()['email'],
                'password' => $request->validated()['password'],
            ],
        ], 201);
    }

    public function show(Driver $driver): View
    {
        $driverWithStats = $this->driverService->getDriverWithStats($driver);

        return view('manager::pages.drivers.show', [
            'driver' => $driverWithStats,
            'stats' => $this->driverService->getStats($driverWithStats),
        ]);
    }

    public function edit(Driver $driver): View
    {
        return view('manager::pages.drivers.edit', [
            'driver' => $driver->load('user:id,name,phone,is_active,approved_at'),
        ]);
    }

    public function update(UpdateDriverRequest $request, Driver $driver): JsonResponse
    {
        $this->driverService->update($driver, $request);

        return response()->json([
            'message' => 'Driver updated successfully.',
        ]);
    }

    public function destroy(Driver $driver): JsonResponse
    {
        try {
            $this->driverService->delete($driver);
        } catch (HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Driver deleted successfully.',
        ]);
    }

    public function toggleStatus(Driver $driver): JsonResponse
    {
        $this->authorize('toggleStatus', $driver);

        $updatedDriver = $this->driverService->toggleStatus($driver);

        return response()->json([
            'message' => 'Driver status updated successfully.',
            'status' => $updatedDriver->status,
        ]);
    }

    public function toggleApproval(Driver $driver): JsonResponse
    {
        $this->authorize('toggleApproval', $driver);

        $updatedDriver = $this->driverService->toggleApproval($driver);

        return response()->json([
            'message' => 'Driver approval updated successfully.',
            'is_approved' => (bool) $updatedDriver->is_approved,
        ]);
    }
}
