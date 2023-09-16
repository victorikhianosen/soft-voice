<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "code" => [
                "required",
                "string",
                Rule::unique('coupons')->where('user_id', $this->user()->id)->ignore(optional($this->coupon)->id, 'id')
            ],
            "discount" => ["required", "numeric", "gt:0"],
            "total" => ["required", "numeric", "gt:0"],
            "expiry_date" => ["required", "date"],
        ];
    }
}
