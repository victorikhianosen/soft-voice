<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "invoice_id"     => ["required", "string", Rule::unique('invoices')->ignore(optional($this->invoice)->id)],
            "customer_id"    => ["required", "numeric", "exists:customers,id"],
            "date"           => ["required", "date", "date_format:Y/m/d"],
            "due_date"       => ["required", "date", "date_format:Y/m/d"],
            "tax_enabled"    => ["required", "boolean"],
            "round_off"      => ["required", "boolean"],
            "terms"          => ["required", "string"],
            "notes"          => ["required", "string"],
            "signatory_name" => ["required", "string"],
            "signature"      => ["file", "max:2048"],
            "status"         => ["required", "string"],

            "recurring"                => ["required", "boolean"],
            "recurring_duration"       => ["required_if:recurring,true", "array"],
            "recurring_duration.mode"  => ["required_if:recurring,true", "string", "in:day,week,month,year"],
            "recurring_duration.value" => ["required_if:recurring,true", "numeric"],

            "additional_charges.*.name"  => ["required", "string"],
            "additional_charges.*.value" => ["required", "numeric"],
            "additional_charges.*.mode"  => ["required", "string", "in:amount,percent"],

            "more_discount.*.name"  => ["required", "string"],
            "more_discount.*.value" => ["required", "numeric"],
            "more_discount.*.mode"  => ["required", "string", "in:amount,percent"],
        ];
    }
}
