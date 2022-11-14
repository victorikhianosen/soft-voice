<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Services\AuthService;
use App\Http\Traits\RespondWithHttpStatus;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Resend Email Verification Link
     *
     * @return JsonResponse
     */
    public function resendEmailVerificationLink(): JsonResponse
    {
        $this->authService->resendEmailVerificationLink();
        return $this->success('Email verification link resent');
    }

    /**
     * Verify Email
     *
     * @param VerifyEmailRequest $request
     * @return void
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $this->authService->verifyEmail($request->all());
        return $this->success('Email verified successfully');
    }
}
