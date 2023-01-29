<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Http\Repositories\ExpenseRepository;
use App\Http\Repositories\InvoiceRepository;

class ReportService
{
    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected ExpenseRepository $expenseRepository,
    )
    {

    }

    public function computeProfitAndLoss(): array
    {
        $payments = $this->invoiceRepository->getUserInvoicePayments();
        $expenses = $this->expenseRepository->getUserExpenses();
        $dates = array_unique(array_merge(array_keys($payments), array_keys($expenses)));
        $data = [];
        foreach ($dates as $date) {
            $inc = $this->getSumByDate($payments, $date);
            $exp = $this->getSumByDate($expenses, $date);
            $data[] = [
                'date'    => $date,
                'income'  => $inc,
                'expense' => $exp,
                'profit'  => $inc - $exp
            ];
        }
        return $data;
    }

    public function computeSales()
    {
        $invoices = $this->invoiceRepository->getUserInvoices();
        $dates = array_keys($invoices);
        $data = [];
        foreach ($dates as $date) {
            $items = $invoices[$date] ?? [];
            $discounts = $this->getInvoiceSumByDate($items, 'discount');
            $sales = $this->getInvoiceSumByDate($items);
            $refunded = array_filter($items, fn($item) => $item['status'] === Status::REFUNDED->value);
            $refunded = $this->getInvoiceSumByDate($refunded);
            $data[] = [
                'date'      => $date,
                'sales'     => $sales,
                'refunded'  => $refunded,
                'discounts' => $discounts,
                'total'     => $sales - $refunded - $discounts
            ];
        }
        return $data;
    }

    public function computeExpense(): array
    {
        $expenses = $this->expenseRepository->getUserExpenses();
        $dates = array_keys($expenses);
        $data = [];
        foreach ($dates as $date) {
            $exp = $this->getSumByDate($expenses, $date);
            $data[] = [
                'date'    => $date,
                'amount'  => $exp,
            ];
        }
        return $data;
    }

    protected function getSumByDate(array $data, string $date, string $key = 'amount'): int
    {
        return array_reduce($data[$date] ?? [], fn ($sum, $item) => $sum + $item[$key], 0);
    }

    protected function getInvoiceSumByDate(array $data, $key = 'amount'): int
    {
        return array_reduce(
            array_merge(
                ...array_column($data, 'items')
            ), fn ($sum, $item) => $sum + $item[$key],
        0);
    }
}
