<?php

namespace App\Http\Requests;

use App\Enums\Status;
use App\Models\Estimate;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ListEstimateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $fixedDays = ["today", "yesterday", "last_7_days", "this_month", "last_month"];
        $columns = (new Estimate)->getFillable();
        array_push($columns, 'id', 'created_at');
        return [
            "current_page"  => ["numeric"],
            "per_page"      => ["numeric"],
            "query"         => ["nullable", "string"],
            "filter"        => ["array"],

            "sort"        => ["array"],
            "sort.column" => ["string", Rule::in($columns)],
            "sort.dir"    => ["string", "in:asc,desc"],

            "filter.status"   => ["array"],
            "filter.status.*" => ["string", Rule::in(Status::estimateTypes())],

            "filter.customers"   => ["array"],
            "filter.customers.*" => ["numeric", "exists:customers,id"],

            "filter.date"            => ["nullable", "array"],
            "filter.date.mode"       => ["nullable", "string", "in:range,fixed"],
            "filter.date.fixed"      => ["nullable", "string", Rule::in($fixedDays)],
            "filter.date.range"      => ["nullable", "array"],
            "filter.date.range.from" => ["nullable", "date", "date_format:Y-m-d"],
            "filter.date.range.to"   => ["nullable", "date", "date_format:Y-m-d"],
        ];
    }
}
