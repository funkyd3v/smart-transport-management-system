<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TripController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.trips.index', $this->service->tripsPageData());
    }

    public function create(): View
    {
        return view('admin::pages.trips.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.trips.index')->with('success', 'Trip created successfully.');
    }

    public function show(Trip $trip): View
    {
        return view('admin::pages.trips.show', compact('trip'));
    }

    public function edit(Trip $trip): View
    {
        return view('admin::pages.trips.create', compact('trip'));
    }

    public function update(Request $request, Trip $trip): RedirectResponse
    {
        return redirect()->route('admin.trips.show', $trip)->with('success', 'Trip updated successfully.');
    }

    public function destroy(Trip $trip): RedirectResponse
    {
        return redirect()->route('admin.trips.index')->with('success', 'Trip archived successfully.');
    }

    public function forceComplete(Trip $trip): RedirectResponse
    {
        return back()->with('success', 'Trip force-completed.');
    }

    public function overrideStatus(Trip $trip): RedirectResponse
    {
        return back()->with('success', 'Trip status overridden.');
    }

    public function reassign(Trip $trip): RedirectResponse
    {
        return back()->with('success', 'Trip reassigned successfully.');
    }
}
