<?php

declare(strict_types=1);

namespace App\Modules\Cashbook\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cashbook\Requests\StoreCashbookRequest;
use App\Modules\Cashbook\Resources\CashbookCollection;
use App\Modules\Cashbook\Resources\CashbookResource;
use App\Modules\Cashbook\Services\CashbookService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashbookController extends Controller
{
    public function __construct(protected CashbookService $service) {}

    public function index(Request $request): CashbookCollection|View
    {
        $filters = $request->only(['date_from', 'date_to', 'type', 'per_page']);
        $entries = $this->service->getPaginatedEntries($filters);

        if ($request->expectsJson()) {
            return new CashbookCollection($entries);
        }

        $summary = $this->service->getMonthlySummary(Carbon::now());

        return view('cashbook::index', [
            'entries' => $entries,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('cashbook::create');
    }

    public function store(StoreCashbookRequest $request): CashbookResource|RedirectResponse
    {
        $entry = $this->service->record($request->validated());

        if ($request->expectsJson()) {
            return new CashbookResource($entry);
        }

        return redirect()->route('cashbooks.index')->with('success', 'Cashbook entry recorded successfully.');
    }

    public function show(Request $request, string $id): CashbookResource|View
    {
        $entry = $this->service->findById($id);

        abort_if($entry === null, 404);

        if ($request->expectsJson()) {
            return new CashbookResource($entry);
        }

        return view('cashbook::show', compact('entry'));
    }

    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $deleted = $this->service->delete($id);

        if ($request->expectsJson()) {
            if (! $deleted) {
                return response()->json(['message' => 'Cashbook entry not found.'], 404);
            }

            return response()->json(['message' => 'Cashbook entry marked as void.']);
        }

        if (! $deleted) {
            return redirect()->route('cashbooks.index')->with('error', 'Cashbook entry not found.');
        }

        return redirect()->route('cashbooks.index')->with('success', 'Entry voided successfully.');
    }
}
