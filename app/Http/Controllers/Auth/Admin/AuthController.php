<?php

namespace App\Http\Controllers\Auth\Admin;

use Illuminate\Http\JsonResponse;
use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Traits\RespondWithHttpStatus;
use App\Http\Requests\Auth\ChangePasswordRequest;

class AuthController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected AuthService $authService)
    {
    }
    /**
     * Login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->adminLogin($request->all());
        return $this->success('Login Successful', $data);
    }

    /**
     * Logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return $this->success('Logout Successful');
    }

    /**
     * Get Authenticated User
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return $this->success(data: $this->authService->getUser());
    }

    /**
     * Change Password
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword($request->all());
        return $this->success('Password updated successfully');
    }
}
