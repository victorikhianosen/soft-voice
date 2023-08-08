<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Exceptions\AuthException;
use App\Http\Factories\UserFactory;
use App\Http\Factories\AdminFactory;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use Illuminate\Support\Facades\Password;
use App\Http\Services\NotificationService;
use App\Http\Requests\UpdateProfileRequest;
use App\Traits\UploadsFile;
use App\Notifications\EmailVerificationNotification;

class AuthService
{
    use UploadsFile;

    public function __construct(protected NotificationService $notificationService)
    {
    }

    public function register(UserFactory $factory, array $data): array
    {
        $data['otp'] = sha1($data['email'].time());
        $user = $factory->create($data);
        if (isset($data['referrer']) && $data['referrer']){
            $referrer = User::where('referral_code', $data['referrer'])->first();
            if ($referrer){
                $referrer->referred()->create(['referred_id' => $user['id']]);
            }
        }
        $url = config('env.spa_url').'/email/verify?email='.$user->email.'&token='.$data['otp'];
        $user->notify(new EmailVerificationNotification($url));
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

    public function adminLogin(array $data): array
    {
        $user = Admin::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new AuthException("Invalid login credentials", 400);
        }
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
        $user->notify(new EmailVerificationNotification($url));
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

    public function sendPasswordResetLink(array $data, string $broker)
    {
        $user = $broker === 'users'
                    ? User::where('email', $data['email'])->first()
                    : Admin::where('email', $data['email'])->first();

        if (!$user) {
            throw new AuthException("We couldn't find a user with this email address", 404);
        }

        Password::broker($broker)
            ->sendResetLink(['email' => $user->email]);
    }

    public function resetPassword(array $data, string $broker)
    {
        $status = Password::broker($broker)->reset($data, function ($user, $password) {
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

    public function updateProfile(UpdateProfileRequest $data)
    {
        $image = $data->file('image');
        $path = null;
        if ($image) $path = $this->saveFIle($image, 'images');
        $data = Arr::only($data->all(), ['name', 'phone', 'address', 'country', 'state', 'postal_code']);
        if ($image && $path) $data['image'] = $path;
        request()->user()->update($data);
    }

    public function getUser(): UserResource|AdminResource
    {
        $user = request()->user();
        return $user instanceof User ? new UserResource($user) : new AdminResource($user);
    }

    public function getToken($user): array
    {
        $ability = $user instanceof User ? 'user' : 'admin';
        return [
            'user'  => ($user instanceof User ? new UserResource($user->fresh()) : new AdminResource($user)),
            'token' => $user->createToken($user->email, [$ability])->plainTextToken
        ];
    }
}