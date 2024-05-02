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

    public function getUserBalance(int $userId): array
    {
        $wallet = $this->model()->where('user_id', $userId)->first();
        return [
            'USD' => $wallet->usd_balance,
            'NGN' => $wallet->ngn_balance,
        ];
    }

    public function creditUserWallet(int $userId, float $amount, string $currency): float
    {
        return $this->model()->where('user_id', $userId)->first()->increment("{$currency}_balance", $amount);
    }

    public function debitUserWallet(int $userId, float $amount, string $currency): float
    {
        return $this->model()->where('user_id', $userId)->first()->decrement("{$currency}_balance", $amount);
    }
}
