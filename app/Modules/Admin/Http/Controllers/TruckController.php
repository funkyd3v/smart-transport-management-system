<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Truck\Models\Truck;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TruckController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.trucks.index', $this->service->trucksPageData());
    }

    public function create(): View
    {
        return view('admin::pages.trucks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.trucks.index')->with('success', 'Truck created successfully.');
    }

    public function show(Truck $truck): View
    {
        return view('admin::pages.trucks.show', compact('truck'));
    }

    public function edit(Truck $truck): View
    {
        return view('admin::pages.trucks.edit', compact('truck'));
    }

    public function update(Request $request, Truck $truck): RedirectResponse
    {
        return redirect()->route('admin.trucks.show', $truck)->with('success', 'Truck updated successfully.');
    }

    public function destroy(Truck $truck): RedirectResponse
    {
        return redirect()->route('admin.trucks.index')->with('success', 'Truck archived successfully.');
    }

    public function updateStatus(Truck $truck): RedirectResponse
    {
        return back()->with('success', 'Truck status updated.');
    }
}
