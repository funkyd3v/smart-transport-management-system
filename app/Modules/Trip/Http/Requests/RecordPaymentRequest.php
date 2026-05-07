<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_ulid' => ['required', 'string', 'exists:trips,ulid'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'transaction_reference' => ['nullable', 'string', 'max:200'],
            'payment_date' => ['required', 'date'],
            'is_advance' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
        ];
    }
}
