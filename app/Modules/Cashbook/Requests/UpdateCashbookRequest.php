<?php

namespace App\Modules\Cashbook\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'in:credit,debit'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'description' => ['sometimes', 'string', 'max:255'],
            'entry_date' => ['sometimes', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }
}
