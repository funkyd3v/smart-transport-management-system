<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Requests\Trip;

use App\Modules\Trip\Enums\TripStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(TripStatus::cases(), 'value'))],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
