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
            'type' => ['required', 'string', 'in:paystack,stripe,flutterwave,paypal'],
            'public_key' => ['required_unless:type,paypal'],
            'secret_key' => ['required_unless:type,paypal'],
            'encryption_key' => ['required_if:type,flutterwave'],
            'username' => ['required_if:type,paypal'],
            'password' => ['required_if:type,paypal'],
            'signature' => ['required_if:type,paypal'],
        ];
    }
}
