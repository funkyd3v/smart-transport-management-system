<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Requests\Truck;

use App\Modules\Truck\Models\Truck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTruckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            if (! is_string($value)) {
                $sanitized[$key] = $value;

                continue;
            }

            $cleaned = strip_tags(trim($value));
            $sanitized[$key] = $key === 'truck_number' ? mb_strtoupper($cleaned) : $cleaned;
        }

        $this->merge($sanitized);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Truck|null $truck */
        $truck = $this->route('truck');

        return [
            'truck_number' => ['required', 'string', 'max:50', Rule::unique('trucks', 'truck_number')->ignore($truck?->id)],
            'truck_type' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:idle,under_workshop'],
        ];
    }
}
