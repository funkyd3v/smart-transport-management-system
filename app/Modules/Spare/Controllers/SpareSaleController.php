<?php

declare(strict_types=1);

namespace App\Modules\Spare\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Spare\Actions\RecordSaleAction;
use App\Modules\Spare\DTOs\RecordSaleDTO;
use App\Modules\Spare\Models\SpareSale;
use App\Modules\Spare\Requests\RecordSaleRequest;
use App\Modules\Spare\Services\SpareSaleService;
use App\Modules\Spare\Services\SpareService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpareSaleController extends Controller
{
    public function __construct(
        private readonly SpareSaleService $saleService,
        private readonly SpareService $spareService,
        private readonly RecordSaleAction $recordSale,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'sale_type_id', 'from', 'to']);

        return view('admin::pages.spare.sales.index', [
            ...$this->saleService->salesPageData($filters),
            ...$this->spareService->salesReferenceData(),
        ]);
    }

    public function create(): View
    {
        return view('admin::pages.spare.sales.create', $this->spareService->salesReferenceData());
    }

    public function store(RecordSaleRequest $request): RedirectResponse
    {
        ($this->recordSale)(RecordSaleDTO::fromRequest($request));

        return redirect()->route('admin.spare.sales.index')
            ->with('toast_success', 'Spare sale recorded successfully.');
    }

    public function show(SpareSale $sale): View
    {
        return view('admin::pages.spare.sales.show', [
            'sale' => $this->saleService->findSaleById((string) $sale->id),
        ]);
    }

    public function destroy(SpareSale $sale): RedirectResponse
    {
        $this->saleService->deleteSale($sale);

        return redirect()->route('admin.spare.sales.index')
            ->with('toast_success', 'Spare sale archived successfully.');
    }
}
