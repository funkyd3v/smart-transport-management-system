<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(['admin', 'manager', 'driver'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required_with:password'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
