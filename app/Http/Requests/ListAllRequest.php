<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListAllRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "current_page" => ["numeric"],
            "per_page"     => ["numeric"],
            "query"        => ["string"],
        ];
    }
}
