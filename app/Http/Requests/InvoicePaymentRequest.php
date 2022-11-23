<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvoicePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'invoice_id'    => ["required", "numeric", "exists:invoices,id"],
            'date'          => ["required", "date", "date_format:Y-m-d"],
            'method'        => ["required", "string", Rule::in(PaymentMethod::types())],
            'amount'        => ["required", "numeric"],
            'notify_client' => ["required", "boolean"],
        ];
    }
}
