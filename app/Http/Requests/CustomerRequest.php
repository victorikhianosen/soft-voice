<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->customer
            ? $this->customer->user_id === request()->user()->id
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
            "name"            => ["required", "string"],
            "email"           => [
                "required",
                "string",
                Rule::unique('customers')->where('user_id', $this->user()->id)->ignore(optional($this->customer)->id, 'id'),
            ],
            "country_code"    => ["required", "numeric"],
            "phone"           => ["required", "numeric"],
            "whatsapp_number" => ["nullable", "numeric"],
            "address"         => ["nullable", "string"],
            "category"        => ["nullable", "string"],
            "website"         => ["nullable", "string"],
            "country"         => ["nullable", "string"],
            "state"           => ["nullable", "string"],
        ];
    }
}
