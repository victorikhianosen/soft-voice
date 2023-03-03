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

    public function creditUserWallet(int $userId, float $amount): float
    {
        return $this->model()->where('user_id', $userId)->first()->increment('balance', $amount);
    }

    public function debitUserWallet(int $userId, float $amount): float
    {
        return $this->model()->where('user_id', $userId)->first()->decrement('balance', $amount);
    }
}
