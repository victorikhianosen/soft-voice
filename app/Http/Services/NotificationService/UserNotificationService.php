<?php

namespace App\Http\Services\NotificationService;

use App\Models\User;
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
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$invoice->user,
			$this->userNotificationBuilder->invoiceViewed($invoice),
			$channel
		);
	}

	public function sendWalletCreditNotification(
		User|int $user,
		float $amount,
		array $channel = ['mail', 'database']
	) {
		if (is_integer($user)) {
			$user = User::find($user);
		}
		$this->send(
			$user,
			$this->userNotificationBuilder->walletCredited($amount),
			$channel
		);
	}
}
