<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingInvoiceUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'auto_payment_reminder' => ['required', 'boolean'],
            'invoice_prefix' => ['required', 'string'],
            'estimate_prefix' => ['required', 'string'],
            'service_category' => ['nullable', 'string'],
            'signature' => ['nullable', 'image', 'max:5120'],
            'signatory_name' => ['nullable', 'string'],
            'preferred_due_date' => ['nullable', 'string'],
            'payment_history_on_invoice_page' => ['required', 'boolean'],
        ];
    }
}
