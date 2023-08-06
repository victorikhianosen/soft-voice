<?php

namespace App\Http\Services;

use App\Http\Repositories\CustomerRepository;
use App\Http\Repositories\EstimateRepository;
use App\Http\Repositories\ExpenseRepository;
use Carbon\Carbon;
use App\Http\Repositories\InvoiceRepository;

class AnalyticsService
{
    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected ExpenseRepository $expenseRepository,
        protected EstimateRepository $estimateRepository,
        protected CustomerRepository $customerRepository,
    ) {
    }

    public function generateAnalytics()
    {
        $invoicesCount = $this->invoiceRepository->getUserInvoicesCount();
        $estimatesCount = $this->estimateRepository->getUserEstimatesCount();
        $customersCount = $this->customerRepository->getUserCustomersCount();

        return [
            "amount_due" => $this->invoiceRepository->getUserDueInvoices(),
            "customers" => $customersCount,
            "invoices" => $invoicesCount,
            "estimates" => $estimatesCount,
            "recent" => [
                "invoices" => $this->invoiceRepository->all(['user_id' => request()->user()->id])->take(5),
                "estimates" => $this->estimateRepository->all(['user_id' => request()->user()->id])->take(5),
            ],
            "charts" => [
                "this_year" => $this->getThisYearSalesAnalytics(),
                "this_month" => $this->getThisMonthSalesAnalytics(),
                "this_week" => $this->getThisWeekSalesAnalytics(),
            ]
        ];
    }

    public function getThisWeekSalesAnalytics()
    {
        $filter = [
            'start_date' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfWeek()->format('Y-m-d'),
        ];

        $invoices = $this->invoiceRepository->getAllUserInvoices($filter);
        $expenses = $this->expenseRepository->getAllUserExpenses($filter);

        $invoiceData = $invoices->groupBy(fn ($el) => Carbon::parse($el->created_at)->format('D'))->toArray();

        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($daysOfWeek as $day) {
            $invoiceData[$day] = [
                "paid" => collect($invoiceData[$day] ?? [])->sum('amount_paid'),
                "due" => collect($invoiceData[$day] ?? [])->sum('amount_due'),
            ];
        }

        $totalPaid = $invoices->sum('amount_paid');
        $totalExpenses = $expenses->sum('amount');

        return [
            "data" => $invoiceData,
            "summary" => [
                "total" => $invoices->sum('total_amount'),
                "due" => $invoices->sum('amount_due'),
                "paid" => $totalPaid,
                "expenses" => $totalExpenses,
                "profit" => $totalPaid - $totalExpenses,
            ]
        ];
    }

    public function getThisMonthSalesAnalytics()
    {
        $filter = [
            'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
        ];

        $invoices = $this->invoiceRepository->getAllUserInvoices($filter);
        $expenses = $this->expenseRepository->getAllUserExpenses($filter);

        $invoiceData = $invoices->groupBy(fn ($el) => Carbon::parse($el->created_at)->format('d'))->toArray();

        for ($day = 1; $day <= date('t'); $day++) {
            $invoiceData[$day] = [
                "paid" => collect($invoiceData[$day] ?? [])->sum('amount_paid'),
                "due" => collect($invoiceData[$day] ?? [])->sum('amount_due'),
            ];
        }

        $totalPaid = $invoices->sum('amount_paid');
        $totalExpenses = $expenses->sum('amount');

        return [
            "data" => $invoiceData,
            "summary" => [
                "total" => $invoices->sum('total_amount'),
                "due" => $invoices->sum('amount_due'),
                "paid" => $totalPaid,
                "expenses" => $totalExpenses,
                "profit" => $totalPaid - $totalExpenses,
            ]
        ];
    }

    public function getThisYearSalesAnalytics()
    {
        $filter = [
            'start_date' => Carbon::now()->startOfYear()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfYear()->format('Y-m-d'),
        ];

        $invoices = $this->invoiceRepository->getAllUserInvoices($filter);
        $expenses = $this->expenseRepository->getAllUserExpenses($filter);

        $invoiceData = $invoices->groupBy(fn ($el) => Carbon::parse($el->created_at)->format('M'))->toArray();

        for ($month = 1; $month <= 12; $month++) {
            $monthKey = date('M', mktime(0, 0, 0, $month, 1, date('Y')));

            $invoiceData[$monthKey] = [
                "paid" => collect($invoiceData[$monthKey] ?? [])->sum('amount_paid'),
                "due" => collect($invoiceData[$monthKey] ?? [])->sum('amount_due'),
            ];
        }

        $totalPaid = $invoices->sum('amount_paid');
        $totalExpenses = $expenses->sum('amount');

        return [
            "data" => $invoiceData,
            "summary" => [
                "total" => $invoices->sum('total_amount'),
                "due" => $invoices->sum('amount_due'),
                "paid" => $totalPaid,
                "expenses" => $totalExpenses,
                "profit" => $totalPaid - $totalExpenses,
            ]
        ];
    }
}
