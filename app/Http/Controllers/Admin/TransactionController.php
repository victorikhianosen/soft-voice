<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExportRequest;
use App\Http\Services\TransactionService;
use App\Http\Traits\RespondWithHttpStatus;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\ListTransactionRequest;

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
        $result = $this->transactionService->getAll($request->all());
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        return $this->success(data: new TransactionResource($transaction));
    }

    public function approve(Transaction $transaction): JsonResponse
    {
        $this->transactionService->approveTransaction($transaction);
        return $this->success("Transaction Approved Successfully");
    }

    public function decline(Transaction $transaction): JsonResponse
    {
        $this->transactionService->declineTransaction($transaction);
        return $this->success("Transaction Decline Successfully");
    }

    public function export(ExportRequest $request): JsonResponse
    {
        $url = $this->transactionService->export($request['format']);
        return $this->success(
            "Temporary file generated, this file will be deleted in 10 minutes",
            ['url' => $url]
        );
    }
}
