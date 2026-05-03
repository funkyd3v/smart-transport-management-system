<?php

namespace App\Modules\Cashbook\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashbookRequest extends FormRequest
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
