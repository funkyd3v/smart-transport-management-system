<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Driver\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DriverController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.drivers.index', $this->service->driversPageData());
    }

    public function create(): View
    {
        return view('admin::pages.drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.drivers.index')->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver): View
    {
        return view('admin::pages.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver): View
    {
        return view('admin::pages.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        return redirect()->route('admin.drivers.show', $driver)->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        return redirect()->route('admin.drivers.index')->with('success', 'Driver archived successfully.');
    }

    public function toggleStatus(Driver $driver): RedirectResponse
    {
        return back()->with('success', 'Driver status updated.');
    }

    public function updateRating(Driver $driver): RedirectResponse
    {
        return back()->with('success', 'Driver rating updated.');
    }
}
