<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $driverId = (int) $this->route('driver')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'license_number' => ['required', 'string', 'max:100', Rule::unique('drivers', 'license_number')->ignore($driverId)],
            'nid_number' => ['required', 'string', 'max:100', Rule::unique('drivers', 'nid_number')->ignore($driverId)],
            'driving_type' => ['required', 'in:permanent,backup'],
            'joining_date' => ['nullable', 'date'],
        ];
    }
}
