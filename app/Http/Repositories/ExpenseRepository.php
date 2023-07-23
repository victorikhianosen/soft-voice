<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\Expense;

class ExpenseRepository extends AbstractRepository
{
    public function __construct(Expense $estimate)
    {
        parent::__construct($estimate);
    }

    public function model()
    {
        return app(Expense::class);
    }

    public function getUserExpenses($filter)
    {
        return request()->user()
            ->expenses()
            ->whereBetween('created_at', [$filter['start_date'], $filter['end_date']])->get()
            ->groupBy(fn($el) => Carbon::parse($el->created_at)->format('M Y'))
            ->toArray();
    }
}
