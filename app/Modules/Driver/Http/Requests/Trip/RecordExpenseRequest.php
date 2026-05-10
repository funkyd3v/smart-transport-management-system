<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Requests\Trip;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            }
        }

        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::in(['fuel', 'toll', 'driver_expense', 'other'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:500'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
