<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', Rule::unique('roles')->ignore(optional($this->role)->id)],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'numeric', 'exists:permissions,id']
        ];
    }
}