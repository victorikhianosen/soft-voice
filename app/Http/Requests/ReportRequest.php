<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'start_date' => ["required", "date", "date_format:Y-m-d"],
            'end_date' => ["required", "date", "date_format:Y-m-d"],
        ];
    }
}
