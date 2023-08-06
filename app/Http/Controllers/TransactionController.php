<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Services\TransactionService;
use App\Traits\RespondWithHttpStatus;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected TransactionService $transactionService
    )
    {
    }

    public function index(ListTransactionRequest $request): JsonResponse
    {
        $result = $this->transactionService->getAll($request->all(), request()->user()->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $this->authorize('process', $transaction);
        return $this->success(data: new TransactionResource($transaction));
    }
}
