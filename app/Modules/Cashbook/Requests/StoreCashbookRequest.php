<?php

namespace App\Modules\Cashbook\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'entry_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'reference_id' => ['nullable', 'string', 'max:26'],
            'reference_type' => ['nullable', 'string', 'max:50'],
        ];
    }
}
