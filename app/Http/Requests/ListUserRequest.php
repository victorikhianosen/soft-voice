<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ListUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $columns = (new User)->getFillable();
        array_push($columns, 'id', 'created_at');
        return [
            "current_page" => ["numeric"],
            "per_page"     => ["numeric"],
            "query"        => ["nullable", "string"],
            "sort"         => ["array"],
            "sort.column"  => ["string", Rule::in($columns)],
            "sort.dir"     => ["string", "in:asc,desc"],
        ];
    }
}
