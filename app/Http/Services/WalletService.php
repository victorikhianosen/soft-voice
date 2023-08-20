<?php

namespace App\Http\Services;

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

    public function processWithdrawal(int $userId, float $amount)
    {
        $balance = $this->walletRepository->getUserBalance($userId);
        if ($amount > $balance) {
            abort(400, "Insufficient wallet balance");
        }
        $this->walletRepository->debitUserWallet($userId, $amount);
        $this->transactionRepository->create([
            'user_id' => $userId,
            'reference' => $this->transactionService->generateReference(),
            'type'    => 'withdrawal',
            'amount'  => $amount,
            'description' => 'Withdrawal Request',
            'method'   => 'wallet',
            'status'  => Status::PENDING->value,
        ]);
        $this->userNotificationService->sendWithdrawalQueuedNotification($userId, $amount);
        $this->userNotificationService->sendWalletDebitNotification($userId, $amount);
    }

    public function processDeposit(int $userId, float $amount)
    {
        $this->transactionRepository->create([
            'user_id' => $userId,
            'reference' => $this->transactionService->generateReference(),
            'type'    => 'deposit',
            'amount'  => $amount,
            'description' => 'Deposit Request',
            'method'   => 'deposit',
            'status'  => Status::PENDING->value,
        ]);
        $this->userNotificationService->sendDepositQueuedNotification($userId, $amount);
    }

    public function fundUserWallet(int $userId, float $amount)
    {
        $this->transactionRepository->create([
            'user_id' => $userId,
            'reference' => $this->transactionService->generateReference(),
            'type'    => 'deposit',
            'amount'  => $amount,
            'description' => 'Invoice Payment Deposit',
            'method'   => 'card',
            'status'  => Status::APPROVED->value,
        ]);
        $this->walletRepository->creditUserWallet($userId, $amount);
        $this->userNotificationService->sendWalletCreditNotification($userId, $amount);
    }
}
