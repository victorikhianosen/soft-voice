<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ExportRequest;
use App\Http\Requests\ExpenseRequest;
use App\Http\Services\ExpenseService;
use App\Http\Resources\ExpenseResource;
use App\Http\Requests\ListExpenseRequest;
use App\Http\Requests\MarkExpenseAsRequest;
use App\Http\Repositories\ExpenseRepository;
use App\Http\Traits\RespondWithHttpStatus;

class ExpenseController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected ExpenseService $expenseService,
        protected ExpenseRepository $expenseRepository
    )
    {
    }

    public function index(ListExpenseRequest $request): JsonResponse
    {
        $result = $this->expenseService->getAll($request->all(), request()->user()->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Expense $expense): JsonResponse
    {
        $this->authorize('process', $expense);
        return $this->success(data: new ExpenseResource($expense));
    }

    public function store(ExpenseRequest $request): JsonResponse
    {
        $data = $this->expenseService->store($request);
        return $this->success("Expense saved", $data);
    }

    public function update(Expense $expense, ExpenseRequest $request): JsonResponse
    {
        $this->authorize('process', $expense);
        $data = $this->expenseService->update($expense, $request);
        return $this->success("Expense updated", $data);
    }

    public function delete(Expense $expense): JsonResponse
    {
        $this->authorize('process', $expense);
        $this->expenseService->delete($expense);
        return $this->success("Expense deleted");
    }

    public function duplicate(Expense $expense): JsonResponse
    {
        $this->authorize('process', $expense);
        $data = $this->expenseService->duplicate($expense);
        return $this->success("Expense duplicated", $data);
    }

    public function markAs(Expense $expense, MarkExpenseAsRequest $request): JsonResponse
    {
        $this->authorize('process', $expense);
        $data = $this->expenseService->markAs($expense, Status::from($request->status));
        return $this->success("Expense status updated", $data);
    }

    public function export(ExportRequest $request): JsonResponse
    {
        $url = $this->expenseService->export($request['format']);
        return $this->success(
            "Temporary file generated, this file will be deleted in 10 minutes",
            ['url' => $url]
        );
    }
}
