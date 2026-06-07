<?php

declare(strict_types=1);

namespace App\Modules\Communication\Http\Requests;

use App\Modules\Communication\Enums\OtpPurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'recipient' => is_string($this->recipient) ? trim($this->recipient) : $this->recipient,
        ]);
    }

    public function rules(): array
    {
        return [
            'purpose' => ['required', 'string', Rule::in(array_map(static fn (OtpPurpose $p): string => $p->value, OtpPurpose::cases()))],
            'recipient' => ['required', 'string', 'max:191'],
            'expires_in_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}
