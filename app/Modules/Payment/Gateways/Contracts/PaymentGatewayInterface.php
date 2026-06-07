<?php

declare(strict_types=1);

namespace App\Modules\Payment\Gateways\Contracts;

use App\Modules\Payment\DTOs\GatewayResponseDTO;
use App\Modules\Payment\Models\Payment;

interface PaymentGatewayInterface
{
    public function key(): string;

    public function initiate(Payment $payment, array $payload = []): GatewayResponseDTO;

    public function validate(Payment $payment, array $payload = []): GatewayResponseDTO;

    public function cancel(Payment $payment, array $payload = []): GatewayResponseDTO;
}
