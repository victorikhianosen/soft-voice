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
        return [
            'all'      => $this->getSummaryByStatus(),
            'paid'     => $this->getSummaryByStatus([Status::PAID, STATUS::PARTIALLY_PAID]),
            'pending'  => $this->getSummaryByStatus([Status::SENT, Status::VIEWED]),
            'refunded' => $this->getSummaryByStatus([Status::REFUNDED]),
            'draft' => $this->getSummaryByStatus([Status::DRAFT]),
            'overdue' => $this->getSummaryByStatus([Status::OVERDUE]),
        ];
    }

    public function getUserInvoicePayments($filter)
    {
        return InvoicePayment::whereHas('invoice', function ($q) {
                $q->whereHas('user', fn ($q) => $q->where('id', request()->user()->id));
            })->whereBetween('created_at', [$filter['start_date'], $filter['end_date']])->get()
            ->groupBy(fn($el) => Carbon::parse($el->created_at)->format('M Y'))
            ->toArray();
    }

    public function getAllUserInvoices($filter)
    {
        return request()->user()
            ->invoices()
            ->whereDate('created_at', '>=', $filter['start_date'])
            ->whereDate('created_at', '<=', $filter['end_date'])
            ->get();
    }

    public function getUserInvoices($filter)
    {
        return $this->getAllUserInvoices($filter)
            ->groupBy(fn($el) => Carbon::parse($el->created_at)->format('M Y'))
            ->toArray();
    }

    public function getUserInvoicesCount()
    {
        return request()->user()->invoices()->count();
    }

    public function getUserDueInvoices()
    {
        return request()->user()
            ->invoices()
            ->get()
            ->sum('amount_due');
    }

    private function getSummaryByStatus(array $statuses = [])
    {
        $query = request()->user()->invoices();

        if ($statuses) {
            $query->where(function($q) use ($statuses) {
                foreach ($statuses as $key => $status) {
                    if ($key === 0) $q->where('status', $status);
                    else $q->orWhere('status', $status);
                }
            });
        }

        $collection = $query->get();

        return [
            'amount' => round($collection->sum('total_amount'), 2),
            'count'  => $collection->count()
        ];
    }
}
