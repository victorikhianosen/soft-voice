<?php

namespace App\Http\Services;

use App\Enums\Currency;
use App\Enums\Status;
use App\Exceptions\CustomerException;
use App\Http\Repositories\TransactionRepository;
use App\Http\Repositories\WalletRepository;
use App\Http\Services\NotificationService\UserNotificationService;

class WalletService
{
    public function __construct(
        protected WalletRepository $walletRepository,
        protected TransactionService $transactionService,
        protected TransactionRepository $transactionRepository,
        protected UserNotificationService $userNotificationService
    ) {
    }

    public function processWithdrawal(int $userId, float $amount, string $currency)
    {
        $balance = $this->walletRepository->getUserBalance($userId)[$currency];
        if ($amount > $balance) {
            abort(400, "Insufficient wallet balance");
        }
        $this->walletRepository->debitUserWallet($userId, $amount, $currency);
        $transaction = $this->transactionRepository->create([
            'user_id' => $userId,
            'reference' => $this->transactionService->generateReference(),
            'type'    => 'withdrawal',
            'amount'  => $amount,
            'description' => 'Withdrawal Request',
            'method'   => 'wallet',
            'status'  => Status::PENDING->value,
            'currency' => $currency,
        ]);
        $this->userNotificationService->sendWithdrawalQueuedNotification($transaction);
        $this->userNotificationService->sendWalletDebitNotification($transaction);
    }

    public function processDeposit(int $userId, float $amount, string $currency)
    {
        $transaction = $this->transactionRepository->create([
            'user_id' => $userId,
            'reference' => $this->transactionService->generateReference(),
            'type'    => 'deposit',
            'amount'  => $amount,
            'description' => 'Deposit Request',
            'method'   => 'deposit',
            'status'  => Status::PENDING->value,
            'currency' => $currency,
        ]);
        $this->userNotificationService->sendDepositQueuedNotification($transaction);
    }

    public function fundUserWallet(int $userId, float $amount, string $currency)
    {
        $transaction = $this->transactionRepository->create([
            'user_id' => $userId,
            'reference' => $this->transactionService->generateReference(),
            'type'    => 'deposit',
            'amount'  => $amount,
            'description' => 'Invoice Payment Deposit',
            'method'   => 'card',
            'status'  => Status::APPROVED->value,
            'currency' => $currency,
        ]);
        $this->walletRepository->creditUserWallet($userId, $amount, $currency);
        $this->userNotificationService->sendWalletCreditNotification($transaction);
    }
}
