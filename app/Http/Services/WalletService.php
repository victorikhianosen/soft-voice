<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Exceptions\CustomerException;
use App\Http\Repositories\TransactionRepository;
use App\Http\Repositories\WalletRepository;

class WalletService
{
    public function __construct(
        protected WalletRepository $walletRepository,
        protected TransactionRepository $transactionRepository
    ){
    }

    public function processWithdrawal(int $userId, float $amount)
    {
        $balance = $this->walletRepository->getUserBalance($userId);
        if ($amount > $balance) {
            abort(400, "Insufficient wallet balance");
        }
        $this->walletRepository->debitUserWallet($userId, $amount);
        $this->transactionRepository->create([
            'user_id' => $userId,
            'type'    => 'withdrawal',
            'amount'  => $amount,
            'description' => 'Withdrawal Request',
            'method'   => 'wallet',
            'status'  => Status::PENDING->value,
        ]);
    }

    public function processDeposit(int $userId, float $amount)
    {
        $this->transactionRepository->create([
            'user_id' => $userId,
            'type'    => 'deposit',
            'amount'  => $amount,
            'description' => 'Deposit Request',
            'method'   => 'deposit',
            'status'  => Status::PENDING->value,
        ]);
    }
}
