<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Enums\Status;
use App\Models\Invoice;
use App\Models\InvoicePayment;

class InvoiceRepository extends AbstractRepository
{
    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
    }

    public function model()
    {
        return app(Invoice::class);
    }

    public function getSummary()
    {
        $invoices = request()->user()->invoices;
        return [
            'all' => $invoices->sum('amount'),
            'paid' => $invoices->where('status', Status::PAID)->sum('amount'),
            'unpaid' => $invoices->where('status', Status::UNPAID)->sum('amount'),
            'refunded' => $invoices->where('status', Status::REFUNDED)->sum('amount'),
        ];
    }

    public function getUserInvoicePayments()
    {
        return InvoicePayment::whereHas('invoice', function ($q) {
                $q->whereHas('user', fn ($q) => $q->where('id', request()->user()->id));
            })->get()
            ->groupBy(fn($el) => Carbon::parse($el->created_at)->format('M Y'))
            ->toArray();
    }

    public function getUserInvoices()
    {
        return request()->user()
            ->invoices()
            ->get()
            ->groupBy(fn($el) => Carbon::parse($el->created_at)->format('M Y'))
            ->toArray();
    }
}
