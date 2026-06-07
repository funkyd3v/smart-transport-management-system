<?php

declare(strict_types=1);

namespace App\Modules\Payment\Repositories;

use App\Modules\Payment\Contracts\PaymentRepositoryInterface;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\PaymentAttempt;
use App\Modules\Payment\Models\PaymentAudit;
use App\Modules\Payment\Models\PaymentTransaction;
use App\Modules\Payment\Models\PaymentWebhook;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function createPayment(array $attributes): Payment
    {
        return Payment::query()->create($attributes);
    }

    public function updatePayment(Payment $payment, array $attributes): Payment
    {
        $payment->fill($attributes)->save();

        return $payment->fresh();
    }

    public function createTransaction(array $attributes): PaymentTransaction
    {
        return PaymentTransaction::query()->create($attributes);
    }

    public function createAttempt(array $attributes): PaymentAttempt
    {
        return PaymentAttempt::query()->create($attributes);
    }

    public function createWebhook(array $attributes): PaymentWebhook
    {
        return PaymentWebhook::query()->create($attributes);
    }

    public function createAudit(array $attributes): PaymentAudit
    {
        return PaymentAudit::query()->create($attributes);
    }
}
