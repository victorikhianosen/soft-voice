<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Exceptions\AuthException;
use App\Http\Factories\UserFactory;
use App\Http\Factories\AdminFactory;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Services\NotificationService;
use App\Notifications\EmailVerificationNotification;

class AuthService
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    public function register(UserFactory|AdminFactory $factory, array $data): array
    {
        $data['otp'] = sha1($data['email'].time());
        $user = $factory->create($data);
        $url = config('env.spa_url').'/email/verify?email='.$user->email.'&token='.$data['otp'];
        $this->notificationService->send(new EmailVerificationNotification($url), $user);
        return $this->getToken($user);
    }

    public function login(array $data): array
    {
        if (!Auth::attempt($data)) {
            throw new AuthException("Invalid login credentials", 400);
        }
        $user = User::where('email', $data['email'])->first();
        return $this->getToken($user);
    }

    public function logout(): void
    {
        request()->user()->currentAccessToken()->delete();
    }

    public function resendEmailVerificationLink()
    {
        $user = request()->user();
        if ($user['email_verified_at']) {
            throw new AuthException("Email already verified", 400);
        }
        $otp = sha1($user['email'].time());
        $user->update([
            'email_verification_token'        => Hash::make($otp),
            'email_verification_token_expiry' => date('Y-m-d H:i:s', strtotime(now().' + 1 hour'))
        ]);
        $url = config('env.spa_url').'/email/verify?email='.$user->email.'&token='.$otp;
        $this->notificationService->send(new EmailVerificationNotification($url), $user);
    }

    public function verifyEmail(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user){
            throw new AuthException("User account not found", 400);
        }
        if ($user['email_verified_at']){
            throw new AuthException("Email already verified", 400);
        }
        if (!Hash::check($data['token'], $user['email_verification_token']) ||
            now()->gt($user['email_verification_token_expiry'])
        ){
            throw new AuthException("Verification token invalid or expired", 400);
        }
        $user->email_verified_at = now();
        $user->save();
    }

    public function sendPasswordResetLink(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            throw new AuthException("We couldn't find a user with this email address", 404);
        }
        Password::sendResetLink(['email' => $user->email]);
    }

    public function resetPassword(array $data)
    {
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
            $user->save();
        });

        if ($status != Password::PASSWORD_RESET) {
            throw new AuthException("Reset token invalid or expired", 400);
        }
    }

    public function changePassword(array $data): void
    {
        $user = request()->user();
        if (!Hash::check($data['old_password'], $user['password'])){
            throw new AuthException("Old password incorrect", 400);
        }
        if (Hash::check($data['new_password'], $user['password'])){
            throw new AuthException("Please enter a different new password", 400);
        }
        $user->update(['password' => Hash::make($data['new_password'])]);
    }

    public function getUser(): UserResource|array
    {
        $user = request()->user();
        return $user instanceof User ? new UserResource($user) : $user;
    }

    public function getToken($user): array
    {
        return [
            'user'  => ($user instanceof User ? new UserResource($user) : $user),
            'token' => $user->createToken('app')->plainTextToken
        ];
    }
}