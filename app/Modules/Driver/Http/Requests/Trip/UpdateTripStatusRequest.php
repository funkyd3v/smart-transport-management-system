<?php

declare(strict_types=1);

namespace App\Modules\Driver\Http\Requests\Trip;

use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTripStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in([TripStatus::InProgress->value, TripStatus::Completed->value])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $trip = $this->trip();

            if ($trip === null) {
                return;
            }

            $current = strtolower(trim((string) $trip->status?->name));
            $target = (string) $this->input('status');

            $isValidTransition = match ($current) {
                TripStatus::Created->value => $target === TripStatus::InProgress->value,
                TripStatus::InProgress->value => $target === TripStatus::Completed->value,
                default => false,
            };

            if (! $isValidTransition) {
                $validator->errors()->add('status', 'The selected trip status transition is invalid.');
            }
        });
    }

    private function trip(): ?Trip
    {
        $trip = $this->route('trip');

        return $trip instanceof Trip ? $trip : null;
    }
}
