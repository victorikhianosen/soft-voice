<?php

namespace App\Http\Services;

use Exception;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\CustomNotification;
use App\Http\Services\NotificationConstructor\AdminNotificationConstructor;
use App\Http\Services\NotificationConstructor\CustomerNotificationConstructor;
use App\Http\Services\NotificationConstructor\UserNotificationConstructor;

class NotificationService
{
    public function __construct(
        protected UserNotificationConstructor $userNotificationConstructor,
        protected AdminNotificationConstructor $adminNotificationConstructor,
        protected CustomerNotificationConstructor $customerNotificationConstructor,
    ) {
    }

    public function sendAdminWelcomeNotification(Admin $admin, string $password)
    {
        $this->send(
            $admin,
            $this->adminNotificationConstructor->welcome($admin, $password)
        );
    }

    private function send(Model $recipient, $data, $channel = ['mail', 'database'])
    {
        $title = $data['title'] ?? null;
        $body = $data['body'] ?? null;
        $action = $data['action'] ?? null;
        if (in_array('mail', $channel) || in_array('database', $channel)) {
            try {
                $recipient->notify(new CustomNotification($title, $body, $action, $channel));
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
