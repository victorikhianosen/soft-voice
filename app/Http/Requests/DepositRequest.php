<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'in:'. implode(',', Currency::currencies())]
        ];
    }
}
