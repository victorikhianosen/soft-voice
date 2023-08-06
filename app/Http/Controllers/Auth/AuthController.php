<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Factories\UserFactory;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Services\AuthService;
use App\Traits\RespondWithHttpStatus;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected AuthService $authService,
        protected UserRepository $userRepository
    )
    {
    }

    /**
     * Register
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register(new UserFactory($this->userRepository), $request->all());
        return $this->success('Registration Successful', $data);
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->all());
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

    /**
     * Update profile
     *
     * @param UpdateProfileRequest $request
     * @return void
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $this->authService->updateProfile($request);
        return $this->success('Profile updated successfully');
    }
}
