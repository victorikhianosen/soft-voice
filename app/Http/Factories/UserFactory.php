<?php

namespace App\Http\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Repositories\UserRepository;

class UserFactory
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function create(array $data): User
    {
        $data['referral_code'] = $this->getUniqueReferralCode();
        $user = $this->userRepository->save($data);

        $plan = Plan::where('slug', 'basic')->first();
        $user->subscriptions()->create([
            'plan_id'   => $plan->id,
            'starts_at' => now(),
            'amount'    => 0,
            'active'    => true,
        ]);

        $user->wallet()->create();
        $user->setting()->create();

        return $user;
    }

    public function getUniqueReferralCode(): string
    {
        do $code = Str::upper(Str::random(8));
        while (User::where('referral_code', $code)->count() > 0);
        return $code;
    }
}
