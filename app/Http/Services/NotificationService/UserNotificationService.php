<?php

namespace App\Http\Services\NotificationService;

use App\Models\Invoice;
use App\Http\Services\NotificationBuilder\UserNotificationBuilder;

class UserNotificationService extends BaseNotificationService
{
	public function __construct(
		protected UserNotificationBuilder $userNotificationBuilder,
	) {
	}

	public function sendInvoiceViewedNotification(
		Invoice $invoice,
		$channel = ['mail', 'database']
	) {
		$this->send(
			$invoice->user,
			$this->userNotificationBuilder->invoiceViewed($invoice),
			$channel
		);
	}
}
