<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name"     => ["required", "string"],
            "email"    => ["required", "string", Rule::unique('customers')->ignore(optional($this->customer)->id)],
            "phone"    => ["required", "string"],
            "address"  => ["required", "string"],
            "category" => ["required", "string"],
            "website"  => ["string"],
            "country"  => ["string"],
            "state"    => ["string"],
        ];
    }
}
