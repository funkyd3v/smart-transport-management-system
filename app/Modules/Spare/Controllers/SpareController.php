<?php

declare(strict_types=1);

namespace App\Modules\Spare\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Spare\Actions\CreateSparePartAction;
use App\Modules\Spare\Actions\DeleteSparePartAction;
use App\Modules\Spare\Actions\UpdateSparePartAction;
use App\Modules\Spare\DTOs\CreateSparePartDTO;
use App\Modules\Spare\DTOs\UpdateSparePartDTO;
use App\Modules\Spare\Models\SparePart;
use App\Modules\Spare\Requests\StoreSparePartRequest;
use App\Modules\Spare\Requests\UpdateSparePartRequest;
use App\Modules\Spare\Services\SpareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpareController extends Controller
{
    public function __construct(
        protected SpareService $service,
        private readonly CreateSparePartAction $createSparePart,
        private readonly UpdateSparePartAction $updateSparePart,
        private readonly DeleteSparePartAction $deleteSparePart,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'condition']);

        return view('admin::pages.spare.inventory.index', $this->service->inventoryPageData($filters));
    }

    public function create(): View
    {
        return view('admin::pages.spare.inventory.create', [
            'categories' => $this->service->categories(),
            'trucks' => $this->service->sourceTrucks(),
        ]);
    }

    public function store(StoreSparePartRequest $request): RedirectResponse
    {
        ($this->createSparePart)(CreateSparePartDTO::fromRequest($request));

        return redirect()->route('admin.spare.inventory.index')
            ->with('toast_success', 'Spare part added successfully.');
    }

    public function edit(SparePart $inventory): View
    {
        return view('admin::pages.spare.inventory.edit', [
            'part' => $inventory,
            'categories' => $this->service->categories(),
            'trucks' => $this->service->sourceTrucks(),
        ]);
    }

    public function update(UpdateSparePartRequest $request, SparePart $inventory): RedirectResponse
    {
        ($this->updateSparePart)($inventory, UpdateSparePartDTO::fromRequest($request));

        return redirect()->route('admin.spare.inventory.index')
            ->with('toast_success', 'Spare part updated successfully.');
    }

    public function destroy(SparePart $inventory): RedirectResponse
    {
        ($this->deleteSparePart)($inventory);

        return redirect()->route('admin.spare.inventory.index')
            ->with('toast_success', 'Spare part archived successfully.');
    }

    public function getPrice(SparePart $part): JsonResponse
    {
        return response()->json([
            'data' => [
                'purchase_price' => (float) $part->purchase_price,
                'available_stock' => (int) $part->quantity,
            ],
        ]);
    }
}
