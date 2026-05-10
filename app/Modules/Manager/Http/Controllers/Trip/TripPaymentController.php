<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\PaymentMethod;
use App\Modules\Trip\Actions\RecordPaymentAction;
use App\Modules\Trip\DTOs\RecordPaymentDTO;
use App\Modules\Trip\Http\Requests\RecordPaymentRequest;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class TripPaymentController extends Controller
{
    public function __construct(private readonly RecordPaymentAction $recordPayment) {}

    public function create(string $tripUlid): View
    {
        $trip = Trip::query()->where('ulid', $tripUlid)->firstOrFail();
        $this->authorize('recordPayment', $trip);

        return view('manager::trips.payments.create', [
            'trip' => $trip,
            'paymentMethods' => PaymentMethod::query()->orderBy('name')->get(),
        ]);
    }

    public function store(RecordPaymentRequest $request): RedirectResponse
    {
        $trip = Trip::query()->where('ulid', $request->validated()['trip_ulid'])->firstOrFail();
        $this->authorize('recordPayment', $trip);

        $dto = RecordPaymentDTO::fromRequest($request);
        ($this->recordPayment)($dto);

        return redirect()->route('manager.trips.show', $trip->ulid)->with('success', 'Payment recorded successfully.');
    }
}
