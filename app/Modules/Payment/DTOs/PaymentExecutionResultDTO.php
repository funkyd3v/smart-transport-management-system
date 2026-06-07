<?php

declare(strict_types=1);

namespace App\Modules\Payment\DTOs;

readonly class PaymentExecutionResultDTO
{
    public function __construct(
        public bool $success,
        public string $status,
        public ?string $paymentId,
        public ?string $transactionId,
        public ?string $customerMsisdn,
        public ?string $amount,
        public ?string $currency,
        public ?string $merchantInvoiceNumber,
        public string $message,
        public array $rawResponse = [],
    ) {}

    public function toGatewayResponse(?string $providerReference = null): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: $this->success,
            status: $this->status,
            gatewayTransactionId: $this->transactionId ?? $this->paymentId,
            providerReference: $providerReference ?? $this->paymentId,
            message: $this->message,
            rawResponse: $this->rawResponse,
        );
    }
}
