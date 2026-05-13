<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTruckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'truck_number' => ['required', 'string', 'max:100', 'unique:trucks,truck_number'],
            'model' => ['nullable', 'string', 'max:120'],
            'brand' => ['nullable', 'string', 'max:120'],
            'year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'capacity_tons' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
