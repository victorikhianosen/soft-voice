<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EstimateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "estimate_id" => ["required", "string", Rule::unique('estimates')->ignore(optional($this->estimate)->id)],
            "customer_id" => ["required", "numeric", "exists:customers,id"],
            "date"        => ["required", "date", "date_format:Y-m-d"],
            "due_date"    => ["required", "date", "date_format:Y-m-d"],
            "tax_enabled" => ["required", "boolean"],
            "notes"       => ["required", "string"],
            "currency"    => ["required", "string"],

            "additional_charges"                => ["nullable", "array"],
            "additional_charges.*.description"  => ["required", "string"],
            "additional_charges.*.value"        => ["required", "numeric"],
            "additional_charges.*.mode"         => ["required", "string", "in:amount,percent"],

            "more_discount"                => ["nullable", "array"],
            "more_discount.*.description"  => ["required", "string"],
            "more_discount.*.value"        => ["required", "numeric"],
            "more_discount.*.mode"         => ["required", "string", "in:amount,percent"],

            "items"            => ["required", "array"],
            "items.*.name"     => ["required", "string"],
            "items.*.quantity" => ["required", "numeric"],
            "items.*.price"    => ["required", "numeric"],
            "items.*.amount"   => ["required", "numeric"],
            "items.*.discount" => ["required", "numeric"],
        ];
    }
}
