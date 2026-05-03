<?php

namespace App\Modules\Due\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDueRequest extends FormRequest
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
