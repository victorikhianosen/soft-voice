<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EstimateExport implements FromArray, WithHeadings
{
    protected $estimates;

    public function __construct(Collection $estimates)
    {
        $this->estimates = $estimates;
    }

    public function array(): array
    {
        return $this->estimates->map(fn($estimate) => [
            'estimate_id'  => $estimate->estimate_id,
            'customer'     => $estimate->customer->name,
            'total_amount' => $estimate->total_amount,
            'date_created' => $estimate->date,
            'due_date'     => $estimate->due_date,
            'status'       => $estimate->status,
        ])->toArray();
    }

    public function headings(): array
    {
        return [
            'Estimate ID',
            'Customer',
            'Total Amount',
            'Date Created',
            'Due Date',
            'Status',
        ];
    }
}
