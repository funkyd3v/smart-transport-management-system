<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            $sanitized[$key] = is_string($value) ? strip_tags(trim($value)) : $value;
        }

        $this->merge($sanitized);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'license_number' => ['required', 'string', 'max:100', 'unique:drivers,license_number'],
            'nid_number' => ['required', 'string', 'max:100', 'unique:drivers,nid_number'],
            'driving_type' => ['required', 'in:permanent,backup'],
            'joining_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['sometimes', 'in:active,inactive'],
            'is_approved' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
