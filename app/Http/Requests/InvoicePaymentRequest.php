<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Validation\Rule;
use App\Enums\NotificationChannel;
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
        $rule = !!optional($this->payment)->id ? "nullable" : "required";
        return [
            'invoice_id'    => [$rule, "numeric", "exists:invoices,id"],
            'date'          => ["required", "date", "date_format:Y-m-d"],
            'method'        => ["required", "string", Rule::in(PaymentMethod::types())],
            'amount'        => ["required", "numeric"],
            'notify_client' => ["required", "boolean"],
            'notify_channels' => ["required_if:notify_client,true", "array", "min:1"],
            'notify_channels.*' => ['required', Rule::in(NotificationChannel::channels())],
        ];
    }
}
