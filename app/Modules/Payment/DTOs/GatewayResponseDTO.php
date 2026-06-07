<?php

declare(strict_types=1);

namespace App\Modules\Payment\DTOs;

readonly class GatewayResponseDTO
{
    public function __construct(
        public bool $success,
        public ?string $status,
        public ?string $gatewayTransactionId,
        public ?string $providerReference,
        public ?string $message,
        public array $rawResponse = [],
    ) {}
}
