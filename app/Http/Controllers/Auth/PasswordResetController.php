<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Traits\RespondWithHttpStatus;
use App\Http\Requests\PasswordResetLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

class PasswordResetController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected AuthService $authService)
    {
    }

    public function sendPasswordResetLink(PasswordResetLinkRequest $request)
    {
        $this->authService->sendPasswordResetLink($request->all(), 'users');
        return $this->success('We have emailed you a password reset link');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->all(), 'users');
        return $this->success('Password Reset Successful');
    }
}
