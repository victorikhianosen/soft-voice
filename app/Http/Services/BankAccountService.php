<?php

namespace App\Http\Services;

use App\Http\Repositories\BankAccountRepository;
use App\Http\Repositories\SettingRepository;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;

class BankAccountService
{
    public function __construct(
        protected BankAccountRepository $bankAccountRepository,
        protected SettingRepository $settingRepository
    )
    {
    }

    public function store(array $data): BankAccountResource
    {
        $data['user_id'] = request()->user()->id;
        $bankAccount = $this->bankAccountRepository->create($data);
        if (isset($data['preferred']) && $data['preferred']) {
            $this->setPreferred($bankAccount->id);
        }
        return new BankAccountResource($bankAccount);
    }

    public function update(BankAccount $bankAccount, array $data): BankAccountResource
    {
        $this->bankAccountRepository->update($bankAccount->id, $data);
        if (isset($data['preferred']) && $data['preferred']) {
            $this->setPreferred($bankAccount->id);
        }
        return new BankAccountResource($bankAccount->refresh());
    }

    public function setPreferred(int|null $bankAccountId = null)
    {
        $this->settingRepository->updateSetting(request()->user()->id, [
            'preferred_bank_id' => $bankAccountId
        ]);
    }

    public function getPreferred(): ?BankAccountResource
    {
        $bankAccountId = $this->settingRepository->getSetting(request()->user()->id)['preferred_bank_id'];
        if ($bankAccountId) {
            return new BankAccountResource($this->bankAccountRepository->find($bankAccountId));
        }
        return null;
    }
}
