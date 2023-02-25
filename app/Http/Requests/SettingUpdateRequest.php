<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
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
            'auto_payment_reminder' => ['required', 'boolean'],
            'invoice_prefix' => ['nullable', 'string'],
            'estimate_prefix' => ['nullable', 'string'],
            'service_category' => ['nullable', 'string'],
            'signature' => ['nullable', 'image', 'max:5120'],
            'signatory_name' => ['nullable', 'string'],
            'preferred_due_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'payment_history_on_invoice_page' => ['required', 'boolean'],
            'auto_enable_tax' => ['required', 'boolean'],
            'auto_enable_bank_account' => ['required', 'boolean'],
            'tax_percentage' => ['required', 'numeric'],
            'theme' => ['required', 'string'],
        ];
    }
}
