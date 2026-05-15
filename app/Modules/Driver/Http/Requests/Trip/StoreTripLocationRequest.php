<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Requests\Trip;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTripLocationRequest extends FormRequest
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
                $sanitized[$key] = trim($value);
            }
        }

        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'speed_kph' => ['nullable', 'numeric', 'min:0', 'max:220'],
            'heading_degrees' => ['nullable', 'integer', 'between:0,359'],
            'captured_at' => ['required', 'date'],
            'device_id' => ['required', 'string', 'min:4', 'max:120'],
            'source' => ['nullable', 'string', 'max:32'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $capturedAt = CarbonImmutable::parse((string) $this->input('captured_at'));

                if ($capturedAt->greaterThan(now()->addMinute())) {
                    $validator->errors()->add('captured_at', 'Captured time cannot be in the future.');
                }

                if ($capturedAt->lessThan(now()->subHours(6))) {
                    $validator->errors()->add('captured_at', 'Captured time is too old.');
                }
            },
        ];
    }
}
