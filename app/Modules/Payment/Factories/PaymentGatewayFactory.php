<?php

declare(strict_types=1);

namespace App\Modules\Payment\Factories;

use App\Modules\Payment\Exceptions\PaymentGatewayException;
use App\Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;

class PaymentGatewayFactory
{
    /**
     * @param  array<string, class-string<PaymentGatewayInterface>>  $gateways
     */
    public function __construct(private readonly array $gateways = []) {}

    public function make(?string $gateway): PaymentGatewayInterface
    {
        $key = strtolower((string) ($gateway ?: config('payment.default_gateway', 'offline')));
        $className = $this->gateways[$key] ?? null;

        if ($className === null) {
            throw new PaymentGatewayException("Unsupported payment gateway [{$key}].");
        }

        $resolved = app($className);

        if (! $resolved instanceof PaymentGatewayInterface) {
            throw new PaymentGatewayException("Payment gateway [{$key}] is not valid.");
        }

        return $resolved;
    }
}
