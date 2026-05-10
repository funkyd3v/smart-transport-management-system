<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\PaymentMethod;
use App\Modules\Trip\Actions\RecordPaymentAction;
use App\Modules\Trip\DTOs\RecordPaymentDTO;
use App\Modules\Trip\Http\Requests\RecordPaymentRequest;
use App\Modules\Trip\Models\Trip;
use Illuminate\Http\JsonResponse;
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

    public function store(RecordPaymentRequest $request, Trip $trip): RedirectResponse|JsonResponse
    {
        $this->authorize('recordPayment', $trip);

        $request->merge([
            'trip_ulid' => $trip->ulid,
            'client_id' => $trip->client_id,
        ]);

        $dto = RecordPaymentDTO::fromRequest($request);
        $payment = ($this->recordPayment)($dto);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Payment recorded successfully.',
                'payment' => [
                    'id' => $payment->id,
                    'date' => (string) $payment->payment_date,
                    'method' => (string) ($payment->paymentMethod?->name ?? ''),
                    'amount' => (float) $payment->amount,
                    'reference' => (string) ($payment->transaction_reference ?? ''),
                    'recorded_by' => (string) ($payment->collector?->name ?? ''),
                ],
            ]);
        }

        return redirect()->route('manager.trips.show', $trip->ulid)->with('success', 'Payment recorded successfully.');
    }
}
