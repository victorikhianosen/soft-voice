<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    public function sendPasswordResetNotification($token)
    {
        $url = env('SPA_URL').'/admin/password/reset?email='.$this->email.'&token='.$token;
        $this->notify(new PasswordResetNotification($url));
    }
}
