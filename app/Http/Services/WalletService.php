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
            throw new CustomerException("Insufficient wallet balance", 400);
        }
        $this->transactionRepository->create([
            'user_id' => request()->user()->id,
            'type'    => 'debit',
            'amount'  => $amount,
            'description' => 'Withdrawal Request',
            'status'  => Status::PENDING->value,
        ]);
    }
}
