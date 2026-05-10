<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Modules\Trip\Actions\GenerateInvoiceAction;
use App\Modules\Trip\DTOs\GenerateInvoiceDTO;
use App\Modules\Trip\Http\Requests\GenerateInvoiceRequest;
use App\Modules\Trip\Models\Invoice;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class TripInvoiceController extends Controller
{
    public function __construct(private readonly GenerateInvoiceAction $generateInvoice) {}

    public function store(GenerateInvoiceRequest $request): RedirectResponse
    {
        $trip = Trip::query()->where('ulid', $request->validated()['trip_ulid'])->firstOrFail();
        $this->authorize('generateInvoice', $trip);

        $dto = GenerateInvoiceDTO::fromRequest($request);
        ($this->generateInvoice)($dto);

        return redirect()->route('manager.trips.invoice.show', $trip)->with('success', 'Invoice generated successfully.');
    }

    public function show(string $invoiceUlid): View
    {
        $invoice = Invoice::query()
            ->where('ulid', $invoiceUlid)
            ->with(['trip.client.user', 'trip.driver.user', 'trip.truck', 'trip.goods'])
            ->firstOrFail();

        $this->authorize('view', $invoice->trip);

        return view('manager::trips.invoices.show', compact('invoice'));
    }

    public function showByTrip(Trip $trip): View
    {
        $this->authorize('view', $trip);

        $invoice = $trip->invoice()->with(['trip.client.user', 'trip.driver.user', 'trip.truck', 'trip.goods'])->firstOrFail();

        return view('manager::trips.invoices.show', compact('invoice'));
    }
}
