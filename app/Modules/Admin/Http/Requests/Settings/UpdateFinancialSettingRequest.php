<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateFinancialSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_prefix' => ['required', 'string', 'max:10'],
            'invoice_number_format' => ['required', 'string', Rule::in(['PREFIX-YEAR-SEQ', 'PREFIX-SEQ'])],
            'default_payment_methods' => ['required', 'array', 'min:1'],
            'default_payment_methods.*' => ['required', 'string', Rule::in(['cash', 'bank_transfer', 'mobile_banking', 'cheque'])],
            'due_reminder_days' => ['required', 'integer', 'min:1', 'max:30'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'fiscal_year_start' => ['required', 'string', Rule::in(['01-01', '07-01'])],
        ];
    }
}
