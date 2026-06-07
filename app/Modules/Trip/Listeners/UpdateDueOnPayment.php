<?php

declare(strict_types=1);

namespace App\Modules\Trip\Listeners;

use App\Modules\Payment\Events\PaymentSucceeded;
use App\Modules\Payment\Models\Payment;
use App\Modules\Trip\Models\DueRecord;
use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Facades\DB;

class UpdateDueOnPayment
{
    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;

        DB::transaction(function () use ($payment): void {
            $trip = Trip::query()->lockForUpdate()->find($payment->trip_id);

            if ($trip === null) {
                return;
            }

            $dueRecord = DueRecord::query()->lockForUpdate()->firstOrCreate(
                ['trip_id' => $trip->id],
                [
                    'ulid' => str()->ulid()->toBase32(),
                    'client_id' => $trip->client_id,
                    'original_due' => max(0, (float) $trip->trip_rate - (float) $trip->advance_payment),
                    'collected_amount' => 0,
                    'remaining_due' => max(0, (float) $trip->trip_rate - (float) $trip->advance_payment),
                    'due_date' => now()->addDays(7)->toDateString(),
                    'is_settled' => false,
                    'settled_at' => null,
                    'notes' => null,
                ]
            );

            $paymentsTotal = (float) Payment::query()->where('trip_id', $trip->id)->sum('amount');
            $remainingDue = max(0, (float) $dueRecord->original_due - $paymentsTotal);

            $dueRecord->forceFill([
                'collected_amount' => $paymentsTotal,
                'remaining_due' => $remainingDue,
                'is_settled' => $remainingDue <= 0,
                'settled_at' => $remainingDue <= 0 ? now() : null,
            ])->save();

            $trip->forceFill([
                'due_amount' => $remainingDue,
            ])->save();
        });
    }
}
