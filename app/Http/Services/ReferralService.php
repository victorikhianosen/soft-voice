<?php

namespace App\Http\Services;

use App\Http\Resources\UserResource;
use App\Models\User;

class ReferralService
{
    public function getByUserId(int $userId)
    {
        $user = User::findOrFail($userId);
        $ids = $user->referred->pluck('referred_id');
        return UserResource::collection(User::whereIn('id', $ids)->get());
    }
}
