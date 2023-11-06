<?php

namespace App\Http\Controllers;

use App\Http\Repositories\BankAccountRepository;
use App\Http\Requests\BankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Http\Services\BankAccountService;
use App\Traits\RespondWithHttpStatus;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected BankAccountService $bankAccountService,
        protected BankAccountRepository $bankAccountRepository
    )
    {
    }

    public function index()
    {
        $data = $this->bankAccountRepository->getAll('*', conditions: ['user_id' => request()->user()->id]);
        return $this->success(data: BankAccountResource::collection($data));
    }

    public function show(BankAccount $bankAccount) {
        $this->authorize('process', $bankAccount);
        return $this->success(data: new BankAccountResource($bankAccount));
    }

    public function store(BankAccountRequest $request)
    {
        $data = $this->bankAccountService->store($request->all());
        return $this->success("Bank account added", $data);
    }

    public function update(BankAccount $bankAccount, BankAccountRequest $request)
    {
        $this->authorize('process', $bankAccount);
        $data = $this->bankAccountService->update($bankAccount, $request->all());
        return $this->success("Bank account updated", $data);
    }

    public function delete(BankAccount $bankAccount)
    {
        $this->authorize('process', $bankAccount);
        $this->bankAccountRepository->destroy($bankAccount);
        return $this->success("Bank account deleted");
    }

    public function setPreferred(BankAccount $bankAccount)
    {
        $this->authorize('process', $bankAccount);
        $this->bankAccountService->setPreferred($bankAccount->id);
        return $this->success("Bank account set as preferred");
    }

    public function getPreferred()
    {
        $data = $this->bankAccountService->getPreferred();
        if ($data) return $this->success(data: $data);
        return $this->success("Preferred bank account not set");
    }

    public function removePreferred()
    {
        $data = $this->bankAccountService->setPreferred(null);
        return $this->success("Preferred bank removed successfully");
    }
}
