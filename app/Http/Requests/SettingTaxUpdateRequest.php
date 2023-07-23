<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingTaxUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'auto_enable_tax' => ['required', 'boolean'],
            'tax_percentage' => ['required', 'numeric'],
        ];
    }
}
