<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends AbstractRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function model()
    {
        return app(User::class);
    }

    public function save($data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email_verification_token'] = Hash::make($data['otp']);
        $data['email_verification_token_expiry'] = now()->addHour();
        unset($data['otp']);
        return $this->create($data);
    }
}
