<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->invoice
            ? $this->invoice->user_id === request()->user()->id
            : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'invoice_id' => [
                'required',
                'string',
                Rule::unique('invoices')->where('user_id', $this->user()->id)->ignore(optional($this->invoice)->id, 'id'),
            ],
            "customer_id"    => ["required", "numeric", "exists:customers,id"],
            "date"           => ["required", "date", "date_format:Y-m-d"],
            "due_date"       => ["required", "date", "date_format:Y-m-d"],
            "tax_enabled"    => ["required", "boolean"],
            "terms"          => ["nullable", "string"],
            "notes"          => ["required", "string"],
            "currency"       => ["required", "string"],
            "signatory_name" => ["nullable", "string"],
            "signature"      => ["nullable", "file", "max:2048"],
            "accept_payment" => ["boolean"],
            "description"    => ["required", "string"],
            "total_amount"    => ["required", "numeric"],

            "recurring"                => ["required", "boolean"],
            "recurring_duration"       => ["required_if:recurring,true", "array"],
            "recurring_duration.mode"  => ["required_if:recurring,true", "string", "in:day,week,month,year"],
            "recurring_duration.value" => ["required_if:recurring,true", "numeric"],

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
