<?php

declare(strict_types=1);

namespace App\Modules\Cashbook\Listeners;

use App\Modules\Cashbook\Enums\CashbookType;
use App\Modules\Cashbook\Services\CashbookService;
use App\Modules\Trip\Events\PaymentRecorded;

class RecordPaymentInCashbook
{
    public function __construct(private readonly CashbookService $cashbookService) {}

    public function handle(PaymentRecorded $event): void
    {
        $payment = $event->payment;

        $this->cashbookService->record([
            'reference_id' => $payment->ulid,
            'reference_type' => 'trip_payment',
            'type' => CashbookType::Credit,
            'amount' => (float) $payment->amount,
            'description' => 'Trip payment received',
            'entry_date' => $payment->payment_date,
            'recorded_by' => $payment->collected_by,
            'note' => $payment->note,
        ]);
    }
}
