<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExportRequest;
use App\Http\Services\ExpenseService;
use App\Http\Services\InvoiceService;
use App\Traits\RespondWithHttpStatus;
use App\Http\Requests\ListUserRequest;
use App\Http\Services\CustomerService;
use App\Http\Services\EstimateService;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\ListUserResource;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\ListExpenseRequest;
use App\Http\Requests\ListInvoiceRequest;
use App\Http\Services\TransactionService;
use App\Http\Requests\ListCustomerRequest;
use App\Http\Requests\ListEstimateRequest;
use App\Http\Resources\BankAccountResource;
use App\Http\Repositories\SettingRepository;
use App\Http\Resources\SubscriptionResource;
use App\Http\Requests\ListTransactionRequest;
use App\Http\Repositories\BankAccountRepository;
use App\Http\Repositories\SubscriptionRepository;
use App\Http\Resources\EstimateResource;
use App\Http\Resources\ExpenseResource;
use App\Models\Estimate;
use App\Models\Expense;

class UserController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected UserService $userService,
        protected UserRepository $userRepository,
        protected TransactionService $transactionService,
        protected SubscriptionRepository $subscriptionRepository,
        protected BankAccountRepository $bankAccountRepository,
        protected CustomerService $customerService,
        protected SettingRepository $settingRepository,
        protected InvoiceService $invoiceService,
        protected ExpenseService $expenseService,
        protected EstimateService $estimateService,
    )
    {
    }

    public function index(ListUserRequest $request): JsonResponse
    {
        $result = $this->userService->getAll($request->all());
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(data: new ListUserResource($user));
    }

    public function restrict(User $user): JsonResponse
    {
        $this->userRepository->update($user, ['active' => 0]);
        return $this->success("User restricted successfully");
    }

    public function reinstate(User $user): JsonResponse
    {
        $this->userRepository->update($user, ['active' => 1]);
        return $this->success("User reinstated successfully");
    }

    public function transactions(ListTransactionRequest $request, User $user): JsonResponse
    {
        $result = $this->transactionService->getAll($request->all(), $user->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function subscriptions(User $user): JsonResponse
    {
        $data = $this->subscriptionRepository->getAllByUser($user->id);
        return $this->success(data: SubscriptionResource::collection($data));
    }

    public function bankAccounts(User $user): JsonResponse
    {
        $data = $this->bankAccountRepository->getAll('*', conditions: ['user_id' => $user->id]);
        return $this->success(data: BankAccountResource::collection($data));
    }

    public function customers(ListCustomerRequest $request, User $user): JsonResponse
    {
        $result = $this->customerService->getAll($request->all(), $user->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function exportCustomers(ExportRequest $request, User $user): JsonResponse
    {
        $url = $this->customerService->export($request['format'], $user->id, 'admin');
        return $this->success(
            "Temporary file generated, this file will be deleted in 10 minutes",
            ['url' => $url]
        );
    }

    public function settings(User $user)
    {
        $data = $this->settingRepository->getSetting($user->id);
        return $this->success(data: new SettingResource($data));
    }

    public function invoices(ListInvoiceRequest $request, User $user)
    {
        $result = $this->invoiceService->getAll($request->all(), $user->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function getInvoiceById(User $user, Invoice $invoice)
    {
        return $this->success(data: new InvoiceResource($invoice));
    }

    public function estimates(ListEstimateRequest $request, User $user)
    {
        $result = $this->estimateService->getAll($request->all(), $user->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function getEstimateById(User $user, Estimate $estimate)
    {
        return $this->success(data: new EstimateResource($estimate));
    }

    public function expenses(ListExpenseRequest $request, User $user)
    {
        $result = $this->expenseService->getAll($request->all(), $user->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function getExpenseById(User $user, Expense $expense)
    {
        return $this->success(data: new ExpenseResource($expense));
    }
}