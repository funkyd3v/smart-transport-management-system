<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateGeneralSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:100'],
            'company_address' => ['required', 'string', 'max:500'],
            'contact_number' => ['required', 'string', 'max:20'],
            'email_address' => ['required', 'email', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'timezone' => ['required', 'string', Rule::in(timezone_identifiers_list())],
            'date_format' => ['required', 'string', Rule::in(['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-M-Y'])],
            'company_logo' => ['nullable', 'file', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }
}
