<?php

declare(strict_types=1);

namespace App\Modules\Payment\DTOs;

readonly class PaymentInitiationResultDTO
{
    public function __construct(
        public bool $success,
        public string $status,
        public ?string $paymentId,
        public ?string $redirectUrl,
        public ?string $callbackUrl,
        public ?string $merchantInvoiceNumber,
        public string $message,
        public array $rawResponse = [],
    ) {}

    public function toGatewayResponse(?string $providerReference = null): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: $this->success,
            status: $this->status,
            gatewayTransactionId: $this->paymentId,
            providerReference: $providerReference ?? $this->paymentId ?? $this->merchantInvoiceNumber,
            message: $this->message,
            rawResponse: $this->rawResponse,
        );
    }
}
