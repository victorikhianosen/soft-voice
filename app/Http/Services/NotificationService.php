<?php

namespace App\Http\Services;

use App\Models\User;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send(Notification $notification, User $user)
    {
        try {
            $user->notify($notification);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
