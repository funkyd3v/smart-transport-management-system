<?php

declare(strict_types=1);

namespace App\Modules\Payment\Gateways\Offline;

use App\Modules\Payment\DTOs\GatewayResponseDTO;
use App\Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;
use App\Modules\Payment\Models\Payment;

class OfflineGateway implements PaymentGatewayInterface
{
    public function key(): string
    {
        return 'offline';
    }

    public function initiate(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: true,
            status: 'succeeded',
            gatewayTransactionId: $payload['gateway_transaction_id'] ?? null,
            providerReference: $payload['provider_reference'] ?? (string) ($payment->transaction_reference ?? $payment->ulid),
            message: 'Offline payment recorded.',
            rawResponse: $payload,
        );
    }

    public function validate(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: true,
            status: 'succeeded',
            gatewayTransactionId: $payload['gateway_transaction_id'] ?? null,
            providerReference: $payload['provider_reference'] ?? $payment->provider_reference,
            message: 'Offline payment validated.',
            rawResponse: $payload,
        );
    }

    public function cancel(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: true,
            status: 'cancelled',
            gatewayTransactionId: $payload['gateway_transaction_id'] ?? null,
            providerReference: $payload['provider_reference'] ?? $payment->provider_reference,
            message: 'Offline payment cancelled.',
            rawResponse: $payload,
        );
    }
}
