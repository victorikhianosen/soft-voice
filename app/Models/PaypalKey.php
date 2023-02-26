<?php

namespace App\Models;

use Throwable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaypalKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'password',
        'signature',
    ];

    public function setUsernameAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['username'] = Crypt::encryptString($value);
        }
    }

    public function getUsernameAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['username']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }

    public function setPasswordAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    public function getPasswordAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['password']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }

    public function setSignatureAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['signature'] = Crypt::encryptString($value);
        }
    }

    public function getSignatureAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['signature']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }
}
