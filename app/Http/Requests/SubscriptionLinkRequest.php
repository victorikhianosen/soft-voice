<?php

namespace App\Http\Requests;

use App\Enums\PaymentGateway;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionLinkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'plan_id' => [
                'required',
                Rule::exists('plans', 'id')->whereNot('slug', 'basic'),
            ],
            'gateway' => ['required', 'in:' . implode(',', PaymentGateway::values())],
        ];
    }
}
