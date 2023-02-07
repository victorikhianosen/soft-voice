<?php

namespace App\Http\Repositories;

use App\Models\Transaction;

class TransactionRepository extends AbstractRepository
{
    public function __construct(Transaction $transaction)
    {
        parent::__construct($transaction);
    }

    public function model()
    {
        return app(Transaction::class);
    }
}
