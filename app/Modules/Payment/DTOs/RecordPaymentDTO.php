<?php

declare(strict_types=1);

namespace App\Modules\Payment\DTOs;

readonly class RecordPaymentDTO
{
    public function __construct(
        public string $payableType,
        public int $payableId,
        public ?int $tripId,
        public int $clientId,
        public int $paymentMethodId,
        public int $collectedBy,
        public float $amount,
        public ?string $transactionReference,
        public string $paymentDate,
        public bool $isAdvance,
        public ?string $note,
        public ?string $gateway,
        public array $gatewayPayload = [],
    ) {}
}
