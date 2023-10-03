<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentKeyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type' => ['required', 'string', 'in:paystack,stripe,paypal'],
            'public_key' => ['required_unless:type,paypal'],
            'secret_key' => ['required_unless:type,paypal'],
            'webhook_id' => ['required_if:type,stripe,paypal'],
            'client_id' => ['required_if:type,paypal'],
            'client_secret' => ['required_if:type,paypal'],
            'active' => ['required', 'boolean'],
        ];
    }
}
