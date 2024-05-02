<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class FlutterwaveKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'public_key',
        'secret_key',
        'encryption_key',
        'active',
    ];

    public function setPublicKeyAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['public_key'] = Crypt::encryptString($value);
        }
    }

    public function getPublicKeyAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['public_key']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }

    public function setSecretKeyAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['secret_key'] = Crypt::encryptString($value);
        }
    }

    public function getSecretKeyAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['secret_key']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }

    public function setEncryptionKeyAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['encryption_key'] = Crypt::encryptString($value);
        }
    }

    public function getEncryptionKeyAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['encryption_key']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }
}
