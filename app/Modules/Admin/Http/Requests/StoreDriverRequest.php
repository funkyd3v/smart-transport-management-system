<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'license_number' => ['required', 'string', 'max:100', 'unique:drivers,license_number'],
            'nid_number' => ['required', 'string', 'max:100', 'unique:drivers,nid_number'],
            'driving_type' => ['required', 'in:permanent,backup'],
            'joining_date' => ['nullable', 'date'],
        ];
    }
}
