<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Modules\Payment\Actions\RecordPaymentAction as PaymentRecordPaymentAction;
use App\Modules\Payment\DTOs\RecordPaymentDTO as PaymentRecordPaymentDTO;
use App\Modules\Payment\Models\Payment;
use App\Modules\Trip\DTOs\RecordPaymentDTO;
use App\Modules\Trip\Models\DueRecord;
use App\Modules\Trip\Models\Trip;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private readonly PaymentRecordPaymentAction $recordPayment,
    ) {}

    public function record(RecordPaymentDTO $dto): Payment
    {
        $trip = Trip::query()->where('ulid', $dto->tripUlid)->firstOrFail();

        /** @var DueRecord|null $dueRecord */
        $dueRecord = DueRecord::query()->where('trip_id', $trip->id)->first();

        if ($dueRecord?->is_settled) {
            throw new RuntimeException('Due already settled for this trip.');
        }

        $paymentDTO = new PaymentRecordPaymentDTO(
            payableType: Trip::class,
            payableId: (int) $trip->id,
            tripId: (int) $trip->id,
            clientId: $dto->clientId,
            paymentMethodId: $dto->paymentMethodId,
            collectedBy: $dto->collectedBy,
            amount: $dto->amount,
            transactionReference: $dto->transactionReference,
            paymentDate: $dto->paymentDate,
            isAdvance: $dto->isAdvance,
            note: $dto->note,
            gateway: 'offline',
            gatewayPayload: [
                'provider_reference' => $dto->transactionReference,
            ],
        );

        return ($this->recordPayment)($paymentDTO);
    }
}
