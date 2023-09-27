<?php

namespace App\Http\Requests;

use App\Enums\PaymentGateway;
use Illuminate\Foundation\Http\FormRequest;

class InvoicePaymentLinkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'invoice_id' => ['required', "numeric", "exists:invoices,id"],
            'amount'     => ['required', 'gt:0'],
            'wallet'     => ['required', 'boolean'],
            'gateway'    => ['required', 'in:' . implode(',', PaymentGateway::values())],
        ];
    }
}
