<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Modules\Trip\DTOs\GenerateInvoiceDTO;
use App\Modules\Trip\Events\InvoiceGenerated;
use App\Modules\Trip\Models\Invoice;
use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceService
{
    public function __construct(
        private readonly RecalculateTripFinancials $recalculateTripFinancials,
    ) {}

    public function generate(GenerateInvoiceDTO $dto): Invoice
    {
        $invoice = DB::transaction(function () use ($dto): Invoice {
            $trip = Trip::query()->with(['client', 'goods'])->where('ulid', $dto->tripUlid)->lockForUpdate()->firstOrFail();

            if ($trip->isInvoiced()) {
                throw new RuntimeException('Invoice already generated for this trip.');
            }

            $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.str_pad((string) ((int) Invoice::query()->count() + 1), 5, '0', STR_PAD_LEFT);

            $invoice = Invoice::query()->create([
                'ulid' => str()->ulid()->toBase32(),
                'invoice_number' => $invoiceNumber,
                'trip_id' => $trip->id,
                'client_id' => $trip->client_id,
                'issued_by' => $dto->issuedBy,
                'subtotal' => (float) $trip->trip_rate,
                'advance_paid' => (float) $trip->advance_payment,
                'due_amount' => max(0, (float) $trip->trip_rate - (float) $trip->advance_payment),
                'total_amount' => (float) $trip->trip_rate,
                'company_logo_path' => $dto->companyLogoPath,
                'authority_signature_path' => $dto->authoritySignaturePath,
                'pdf_path' => null,
                'issued_at' => now(),
                'created_at' => now(),
            ]);

            $trip->invoice_generated_at = now();
            $trip->save();

            $this->recalculateTripFinancials->execute($trip);

            return $invoice->fresh();
        });

        event(new InvoiceGenerated($invoice));

        return $invoice;
    }
}
