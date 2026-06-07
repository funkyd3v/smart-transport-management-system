<?php

declare(strict_types=1);

namespace App\Modules\Communication\Http\Requests;

use App\Modules\Communication\Enums\OtpPurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'recipient' => is_string($this->recipient) ? trim($this->recipient) : $this->recipient,
            'code' => is_string($this->code) ? trim($this->code) : $this->code,
        ]);
    }

    public function rules(): array
    {
        return [
            'purpose' => ['required', 'string', Rule::in(array_map(static fn (OtpPurpose $p): string => $p->value, OtpPurpose::cases()))],
            'recipient' => ['required', 'string', 'max:191'],
            'code' => ['required', 'digits:6'],
        ];
    }
}
