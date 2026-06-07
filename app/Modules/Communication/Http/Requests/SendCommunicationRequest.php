<?php

declare(strict_types=1);

namespace App\Modules\Communication\Http\Requests;

use App\Modules\Communication\Enums\CommunicationChannel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendCommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'recipient' => is_string($this->recipient) ? trim($this->recipient) : $this->recipient,
            'subject' => is_string($this->subject) ? trim(strip_tags($this->subject)) : $this->subject,
            'body' => is_string($this->body) ? trim($this->body) : $this->body,
            'template_key' => is_string($this->template_key) ? trim($this->template_key) : $this->template_key,
            'provider' => is_string($this->provider) ? trim($this->provider) : $this->provider,
        ]);
    }

    public function rules(): array
    {
        return [
            'channel' => ['required', 'string', Rule::in(array_map(static fn (CommunicationChannel $c): string => $c->value, CommunicationChannel::cases()))],
            'recipient' => ['required', 'string', 'max:191'],
            'subject' => ['nullable', 'string', 'max:191'],
            'body' => ['required', 'string'],
            'provider' => ['nullable', 'string', 'max:60'],
            'template_key' => ['nullable', 'string', 'max:120'],
            'template_data' => ['nullable', 'array'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'string', 'max:100'],
            'scheduled_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
