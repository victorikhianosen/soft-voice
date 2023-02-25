<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'bank_name' => ['required', 'string'],
            'account_name' => ['required', 'string'],
            'account_number' => ['required', 'string', Rule::unique('bank_accounts')->ignore(optional($this->bank_account)->id)],
            'ifsc_code' => ['nullable', 'string'],
            'other_info' => ['nullable'],
        ];
    }
}
