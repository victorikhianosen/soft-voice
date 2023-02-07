<?php

namespace App\Http\Repositories;

use App\Models\Wallet;

class WalletRepository extends AbstractRepository
{
    public function __construct(Wallet $wallet)
    {
        parent::__construct($wallet);
    }

    public function model()
    {
        return app(Wallet::class);
    }

    public function getUserBalance(int $userId): float
    {
        return $this->model()->where('user_id', $userId)->first()->balance;
    }
}
