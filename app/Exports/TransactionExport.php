<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromArray, WithHeadings
{
    protected $transactions;

    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    public function array(): array
    {
        return $this->transactions->map(fn($transaction) => [
            'id'   => $transaction->id,
            'reference'   => $transaction->reference,
            'user'   => $transaction->user->name,
            'type'   => $transaction->type,
            'amount'   => number_format($transaction->amount, 2),
            'description'   => $transaction->description,
            'method'     => $transaction->method,
            'status'     => $transaction->status,
            'date_created' => $transaction->created_at,
        ])->toArray();
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Reference ID',
            'User',
            'Type',
            'Amount',
            'Description',
            'Method',
            'Status',
            'Date Created',
        ];
    }
}
