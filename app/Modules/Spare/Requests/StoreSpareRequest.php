<?php

namespace App\Modules\Spare\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpareRequest extends FormRequest
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
