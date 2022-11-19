<?php

namespace App\Http\Services;

use Exception;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;

class NotificationService
{
    public function send(Notification $notification, Model $user)
    {
        try {
            $user->notify($notification);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
