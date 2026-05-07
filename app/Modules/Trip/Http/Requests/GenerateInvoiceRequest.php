<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_ulid' => ['required', 'string', 'exists:trips,ulid'],
            'company_logo' => ['nullable', 'file', 'mimes:jpeg,png,pdf', 'max:2048'],
            'authority_signature' => ['nullable', 'file', 'mimes:jpeg,png,pdf', 'max:2048'],
        ];
    }
}
