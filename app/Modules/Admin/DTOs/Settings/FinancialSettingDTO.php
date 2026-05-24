<?php

declare(strict_types=1);

namespace App\Modules\Admin\DTOs\Settings;

use App\Modules\Admin\Http\Requests\Settings\UpdateFinancialSettingRequest;

final readonly class FinancialSettingDTO
{
    /**
     * @param  list<string>  $defaultPaymentMethods
     */
    public function __construct(
        public string $invoicePrefix,
        public string $invoiceNumberFormat,
        public array $defaultPaymentMethods,
        public int $dueReminderDays,
        public ?float $taxRate,
        public string $fiscalYearStart,
    ) {}

    public static function fromRequest(UpdateFinancialSettingRequest $request): self
    {
        return new self(
            invoicePrefix: (string) $request->validated('invoice_prefix'),
            invoiceNumberFormat: (string) $request->validated('invoice_number_format'),
            defaultPaymentMethods: array_values((array) $request->validated('default_payment_methods')),
            dueReminderDays: (int) $request->validated('due_reminder_days'),
            taxRate: $request->validated('tax_rate') !== null ? (float) $request->validated('tax_rate') : null,
            fiscalYearStart: (string) $request->validated('fiscal_year_start'),
        );
    }
}
