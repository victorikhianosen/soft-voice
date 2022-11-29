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
}
