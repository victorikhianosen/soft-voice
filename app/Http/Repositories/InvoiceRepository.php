<?php

namespace App\Http\Repositories;

use App\Enums\Status;
use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

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
}
