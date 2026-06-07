<?php

declare(strict_types=1);

namespace App\Modules\Payment\Contracts;

use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\PaymentAttempt;
use App\Modules\Payment\Models\PaymentAudit;
use App\Modules\Payment\Models\PaymentTransaction;
use App\Modules\Payment\Models\PaymentWebhook;

interface PaymentRepositoryInterface
{
    public function createPayment(array $attributes): Payment;

    public function updatePayment(Payment $payment, array $attributes): Payment;

    public function createTransaction(array $attributes): PaymentTransaction;

    public function createAttempt(array $attributes): PaymentAttempt;

    public function createWebhook(array $attributes): PaymentWebhook;

    public function createAudit(array $attributes): PaymentAudit;
}
