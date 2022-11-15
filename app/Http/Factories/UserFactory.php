<?php

namespace App\Http\Factories;

use App\Http\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function create(array $data): User
    {
        $data['referral_code'] = $this->getUniqueReferralCode();
        $user = $this->userRepository->save($data);

        // create wallet

        return $user;
    }

    public function getUniqueReferralCode(): string
    {
        do $code = Str::upper(Str::random(8));
        while (User::where('referral_code', $code)->count() > 0);
        return $code;
    }
}
