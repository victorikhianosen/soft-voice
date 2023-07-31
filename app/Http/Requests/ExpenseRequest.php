<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "customer_id" => ["required", "numeric", "exists:customers,id"],
            "date"        => ["required", "date", "date_format:Y-m-d"],
            "notes"       => ["required", "string"],
            "amount"      => ["required", "numeric"],
            "attachment"  => ["file", "max:5120"],
            'currency' => ['required', Rule::in(Currency::currencies())],

            "recurring"                => ["required", "boolean"],
            "recurring_duration"       => ["required_if:recurring,true", "array"],
            "recurring_duration.mode"  => ["required_if:recurring,true", "string", "in:day,week,month,year"],
            "recurring_duration.value" => ["required_if:recurring,true", "numeric"],
        ];
    }
}
