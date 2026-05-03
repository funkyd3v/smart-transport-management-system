<?php

namespace App\Modules\Due\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
