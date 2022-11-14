<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'    => ['required', 'email', 'unique:users,email', 'max:255'],
            'name'     => ['required', 'max:255'],
            'password' => ['required', Password::min(8)]
        ];
    }
}
