<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromArray, WithHeadings
{
    protected $invoices;

    public function __construct(Collection $invoices)
    {
        $this->invoices = $invoices;
    }

    public function array(): array
    {
        return $this->invoices->map(fn($invoice) => [
            'invoice_id'   => $invoice->invoice_id,
            'customer'     => $invoice->customer->name,
            'amount'       => $invoice->amount,
            'date_created' => $invoice->date,
            'due_date'     => $invoice->due_date,
            'status'       => $invoice->status,
        ])->toArray();
    }

    public function headings(): array
    {
        return [
            'Invoice ID',
            'Customer',
            'Amount',
            'Date Created',
            'Due Date',
            'Status',
        ];
    }
}
