<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Spare\Models\SpareSale;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SpareSaleController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.spare.sales.index', $this->service->spareSalesData());
    }

    public function create(): View
    {
        return view('admin::pages.spare.sales.create');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('admin.spare.sales.index')->with('success', 'Spare sale recorded successfully.');
    }

    public function show(SpareSale $sale): View
    {
        return view('admin::pages.spare.sales.index', compact('sale'));
    }

    public function edit(SpareSale $sale): View
    {
        return view('admin::pages.spare.sales.create', compact('sale'));
    }

    public function update(SpareSale $sale): RedirectResponse
    {
        return redirect()->route('admin.spare.sales.index')->with('success', 'Spare sale updated successfully.');
    }

    public function destroy(SpareSale $sale): RedirectResponse
    {
        return redirect()->route('admin.spare.sales.index')->with('success', 'Spare sale archived successfully.');
    }
}
