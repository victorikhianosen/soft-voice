<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => ["required", "string"],
            "email" => ["required", "string", "email", Rule::unique('admins')->ignore(optional($this->admin)->id)],
            "role_id" => ["required", "numeric", "exists:roles,id"],
        ];
    }
}
