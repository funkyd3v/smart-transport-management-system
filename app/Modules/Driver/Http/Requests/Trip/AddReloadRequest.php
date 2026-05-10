<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Requests\Trip;

use Illuminate\Foundation\Http\FormRequest;

class AddReloadRequest extends FormRequest
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
            'location' => ['required', 'string', 'max:255'],
            'reload_amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
            'reloaded_at' => ['required', 'date', 'before_or_equal:now'],
        ];
    }
}
