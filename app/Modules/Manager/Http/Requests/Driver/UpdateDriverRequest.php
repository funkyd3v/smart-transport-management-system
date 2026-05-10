<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Requests\Driver;

use App\Modules\Driver\Models\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
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
        /** @var Driver|null $driver */
        $driver = $this->route('driver');

        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'license_number' => ['required', 'string', 'max:100', Rule::unique('drivers', 'license_number')->ignore($driver?->id)],
            'nid_number' => ['required', 'string', 'max:100', Rule::unique('drivers', 'nid_number')->ignore($driver?->id)],
            'driving_type' => ['required', 'in:permanent,backup'],
            'joining_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['sometimes', 'in:active,inactive'],
            'is_approved' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
