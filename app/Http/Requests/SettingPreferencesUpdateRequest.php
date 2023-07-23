<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingPreferencesUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'currency' => ['required', Rule::in(Currency::currencies())],
            'timezone' => ['nullable', 'string'],
            'date_format' => ['nullable', 'string'],
            'financial_year' => ['nullable', 'string'],
        ];
    }
}
