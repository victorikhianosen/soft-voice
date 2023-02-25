<?php

namespace App\Http\Repositories;

use App\Models\BankAccount;

class BankAccountRepository extends AbstractRepository
{
    public function __construct(BankAccount $bankAccount)
    {
        parent::__construct($bankAccount);
    }

    public function model()
    {
        return app(BankAccount::class);
    }
}
