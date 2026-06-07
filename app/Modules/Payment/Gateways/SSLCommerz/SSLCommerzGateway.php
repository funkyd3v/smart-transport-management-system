<?php

declare(strict_types=1);

namespace App\Modules\Payment\Gateways\SSLCommerz;

use App\Modules\Payment\DTOs\GatewayResponseDTO;
use App\Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;
use App\Modules\Payment\Models\Payment;

class SSLCommerzGateway implements PaymentGatewayInterface
{
    public function key(): string
    {
        return 'sslcommerz';
    }

    public function initiate(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: true,
            status: 'succeeded',
            gatewayTransactionId: $payload['gateway_transaction_id'] ?? null,
            providerReference: $payload['provider_reference'] ?? ('SSLCZ-'.$payment->ulid),
            message: 'SSLCommerz initiation prepared.',
            rawResponse: [
                'sandbox' => (bool) config('payment.sslcommerz.sandbox', true),
                'store_id' => (string) config('payment.sslcommerz.store_id'),
            ],
        );
    }

    public function validate(Payment $payment, array $payload = []): GatewayResponseDTO
    {
        return new GatewayResponseDTO(
            success: true,
            status: 'succeeded',
            gatewayTransactionId: $payload['gateway_transaction_id'] ?? null,
            providerReference: $payload['provider_reference'] ?? $payment->provider_reference,
            message: 'SSLCommerz payment validated.',
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
            message: 'SSLCommerz payment cancelled.',
            rawResponse: $payload,
        );
    }
}
