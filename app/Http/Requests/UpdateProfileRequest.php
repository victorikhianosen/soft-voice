<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'image' => ['nullable', 'image', 'max:5120'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
        ];
    }
}
