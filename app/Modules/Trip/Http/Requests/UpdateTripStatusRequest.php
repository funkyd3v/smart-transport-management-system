<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Requests;

use App\Modules\Trip\Enums\TripStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTripStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_ulid' => ['required', 'string', 'exists:trips,ulid'],
            'status' => ['required', Rule::in(array_column(TripStatus::cases(), 'value'))],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
