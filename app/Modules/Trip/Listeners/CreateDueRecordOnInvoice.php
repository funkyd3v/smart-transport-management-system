<?php

declare(strict_types=1);

namespace App\Modules\Trip\Listeners;

use App\Modules\Trip\Events\InvoiceGenerated;
use App\Modules\Trip\Models\DueRecord;
use Illuminate\Support\Facades\DB;

class CreateDueRecordOnInvoice
{
    public function handle(InvoiceGenerated $event): void
    {
        $invoice = $event->invoice->loadMissing('trip');
        $trip = $invoice->trip;

        if ($trip === null) {
            return;
        }

        DB::transaction(function () use ($invoice, $trip): void {
            DueRecord::query()->updateOrCreate(
                ['trip_id' => $trip->id],
                [
                    'ulid' => str()->ulid()->toBase32(),
                    'client_id' => $trip->client_id,
                    'original_due' => (float) $invoice->due_amount,
                    'collected_amount' => 0,
                    'remaining_due' => (float) $invoice->due_amount,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'is_settled' => (float) $invoice->due_amount <= 0,
                    'settled_at' => (float) $invoice->due_amount <= 0 ? now() : null,
                    'notes' => null,
                ]
            );
        });
    }
}
