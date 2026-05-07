<?php

declare(strict_types=1);

namespace App\Modules\Trip\DTOs;

use App\Modules\Trip\Http\Requests\RecordPaymentRequest;

readonly class RecordPaymentDTO
{
    public function __construct(
        public string $tripUlid,
        public int $clientId,
        public int $paymentMethodId,
        public int $collectedBy,
        public float $amount,
        public ?string $transactionReference,
        public string $paymentDate,
        public bool $isAdvance,
        public ?string $note,
    ) {}

    public static function fromRequest(RecordPaymentRequest $request): self
    {
        $data = $request->validated();

        return new self(
            tripUlid: (string) $data['trip_ulid'],
            clientId: (int) $data['client_id'],
            paymentMethodId: (int) $data['payment_method_id'],
            collectedBy: (int) $request->user()->id,
            amount: (float) $data['amount'],
            transactionReference: $data['transaction_reference'] ?? null,
            paymentDate: (string) $data['payment_date'],
            isAdvance: (bool) ($data['is_advance'] ?? false),
            note: $data['note'] ?? null,
        );
    }
}
