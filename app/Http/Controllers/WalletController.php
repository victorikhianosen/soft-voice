<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Services\WalletService;
use App\Http\Requests\WithdrawalRequest;
use App\Traits\RespondWithHttpStatus;
use App\Http\Repositories\WalletRepository;
use App\Http\Requests\DepositRequest;

class WalletController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected WalletRepository $walletRepository,
        protected WalletService $walletService,
    ){
    }

    public function getBalance(): JsonResponse
    {
        $balance = $this->walletRepository->getUserBalance(request()->user()->id);
        return $this->success(data: compact('balance'));
    }

    public function withdraw(WithdrawalRequest $request): JsonResponse
    {
        $this->walletService->processWithdrawal(request()->user()->id, $request->amount);
        return $this->success("Withdrawal Queued Successfully");
    }

    public function deposit(DepositRequest $request): JsonResponse
    {
        $this->walletService->processDeposit(request()->user()->id, $request->amount);
        return $this->success("Deposit Queued Successfully");
    }
}
