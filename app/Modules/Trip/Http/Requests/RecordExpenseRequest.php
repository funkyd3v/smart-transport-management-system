<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_ulid' => ['required', 'string', 'exists:trips,ulid'],
            'category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'description' => ['nullable', 'string', 'max:300'],
            'expense_date' => ['required', 'date'],
            'receipt' => ['nullable', 'file', 'mimes:jpeg,png,pdf', 'max:2048'],
        ];
    }
}
