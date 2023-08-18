<?php

namespace App\Http\Services\NotificationService;

use App\Models\Admin;
use App\Http\Services\NotificationBuilder\AdminNotificationBuilder;

class AdminNotificationService extends BaseNotificationService
{
	public function __construct(
		protected AdminNotificationBuilder $adminNotificationBuilder,
	) {
	}

	public function sendWelcomeNotification(
        Admin $admin, string $password, $channel = ['mail', 'database']
    ) {
        $this->send(
            $admin,
            $this->adminNotificationBuilder->welcome($admin, $password),
            $channel
        );
    }
}
