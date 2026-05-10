<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Modules\Trip\DTOs\RecordPaymentDTO;
use App\Modules\Trip\Events\PaymentRecorded;
use App\Modules\Trip\Models\DueRecord;
use App\Modules\Trip\Models\Payment;
use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private readonly RecalculateTripFinancials $recalculateTripFinancials,
    ) {}

    public function record(RecordPaymentDTO $dto): Payment
    {
        $payment = DB::transaction(function () use ($dto): Payment {
            $trip = Trip::query()->where('ulid', $dto->tripUlid)->lockForUpdate()->firstOrFail();

            /** @var DueRecord $dueRecord */
            $dueRecord = DueRecord::query()->where('trip_id', $trip->id)->lockForUpdate()->firstOrFail();

            if ($dueRecord->is_settled) {
                throw new RuntimeException('Due already settled for this trip.');
            }

            $payment = Payment::query()->create([
                'ulid' => str()->ulid()->toBase32(),
                'trip_id' => $trip->id,
                'client_id' => $dto->clientId,
                'collected_by' => $dto->collectedBy,
                'payment_method_id' => $dto->paymentMethodId,
                'amount' => $dto->amount,
                'transaction_reference' => $dto->transactionReference,
                'payment_date' => $dto->paymentDate,
                'is_advance' => $dto->isAdvance,
                'note' => $dto->note,
            ]);

            $this->recalculateTripFinancials->execute($trip);

            return $payment->fresh();
        });

        event(new PaymentRecorded($payment));

        return $payment;
    }
}
