<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Spare\Models\SparePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SpareController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.spare.index', $this->service->spareInventoryData());
    }

    public function create(): View
    {
        return view('admin::pages.spare.create');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('admin.spare.index')->with('success', 'Spare part added successfully.');
    }

    public function show(SparePart $spare): View
    {
        return view('admin::pages.spare.index', compact('spare'));
    }

    public function edit(SparePart $spare): View
    {
        return view('admin::pages.spare.create', compact('spare'));
    }

    public function update(SparePart $spare): RedirectResponse
    {
        return redirect()->route('admin.spare.index')->with('success', 'Spare part updated successfully.');
    }

    public function destroy(SparePart $spare): RedirectResponse
    {
        return redirect()->route('admin.spare.index')->with('success', 'Spare part archived successfully.');
    }
}
