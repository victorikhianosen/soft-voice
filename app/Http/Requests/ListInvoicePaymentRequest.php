<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListInvoicePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "invoice_id" => ["required", "exists:invoices,id"]
        ];
    }
}
