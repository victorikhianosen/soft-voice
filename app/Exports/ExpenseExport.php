<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseExport implements FromArray, WithHeadings
{
    protected $expenses;

    public function __construct(Collection $expenses)
    {
        $this->expenses = $expenses;
    }

    public function array(): array
    {
        return $this->expenses->map(fn($estimate) => [
            'customer'  => $estimate->customer->name,
            'amount'    => $estimate->amount,
            'date'      => $estimate->date,
            'recurring' => $estimate->recurring == 1 ? "Yes" : "No",
            'status'    => $estimate->status,
        ])->toArray();
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Amount',
            'Date',
            'Recurring',
            'Status',
        ];
    }
}
