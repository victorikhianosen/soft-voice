<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'referral_code',
        'phone',
        'country',
        'state',
        'postal_code',
        'image',
        'address',
        'active',
        'email_verification_token',
        'email_verification_token_expiry'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'active_subscription'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function bank_accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function stripeKey(): HasOne
    {
        return $this->hasOne(StripeKey::class);
    }

    public function payStackKey(): HasOne
    {
        return $this->hasOne(PaystackKey::class);
    }

    public function flutterwaveKey(): HasOne
    {
        return $this->hasOne(FlutterwaveKey::class);
    }

    public function payPalKey(): HasOne
    {
        return $this->hasOne(PaypalKey::class);
    }

    public function referred(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function subscribed()
    {
        return Carbon::parse($this->active_subscriptions->ends_at)->gte(now());
    }

    public function getActiveSubscriptionAttribute()
    {
        return $this->subscriptions()->whereNotNull('ends_at')->latest()->first();
    }

    public function sendPasswordResetNotification($token)
    {
        $url = env('SPA_URL').'/password/reset?email='.$this->email.'&token='.$token;
        $this->notify(new PasswordResetNotification($url));
    }
}
