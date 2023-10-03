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
        'client_id',
        'client_secret',
        'webhook_id',
        'active',
    ];

    public function setClientIdAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['client_id'] = Crypt::encryptString($value);
        }
    }

    public function getClientIdAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['client_id']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }

    public function setClientSecretAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['client_secret'] = Crypt::encryptString($value);
        }
    }

    public function getClientSecretAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['client_secret']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }

    public function setWebhookIdAttribute(?string $value)
    {
        if ($value) {
            $this->attributes['webhook_id'] = Crypt::encryptString($value);
        }
    }

    public function getWebhookIdAttribute()
    {
        try {
            $decrypted = Crypt::decryptString($this->attributes['webhook_id']);
        } catch (Throwable $e) {
            $decrypted = "";
        }
        return $decrypted;
    }
}
