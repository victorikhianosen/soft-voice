<?php

namespace App\Http\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email_verification_token'] = Hash::make($data['otp']);
        $data['email_verification_token_expiry'] = now()->addHour();
        $data['referral_code'] = $this->getUniqueReferralCode();
        unset($data['otp']);
        $user = User::create($data);

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
