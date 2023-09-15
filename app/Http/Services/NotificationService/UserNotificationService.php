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

	public function sendInvoiceOverdueNotification(
        Invoice $invoice,
        array $channel = ['mail']
    ) {
        $this->send(
            $invoice->user,
            $this->userNotificationBuilder->invoiceOverdue($invoice),
            $channel
        );
    }

	public function sendInvoicepaymentRecordedNotification(
        Invoice $invoice,
        array $channel = ['mail']
    ) {
        $this->send(
            $invoice->user,
            $this->userNotificationBuilder->invoicepaymentRecorded($invoice),
            $channel
        );
    }

	public function sendInvoicepaymentReceivedNotification(
        Invoice $invoice,
        array $channel = ['mail']
    ) {
        $this->send(
            $invoice->user,
            $this->userNotificationBuilder->invoicepaymentReceived($invoice),
            $channel
        );
    }

	public function sendInvoicepaymentCompletedNotification(
        Invoice $invoice,
        array $channel = ['mail']
    ) {
        $this->send(
            $invoice->user,
            $this->userNotificationBuilder->invoicepaymentCompleted($invoice),
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

	public function sendWalletDebitNotification(
		User|int $user,
		float $amount,
		array $channel = ['mail', 'database']
	) {
		if (is_integer($user)) {
			$user = User::find($user);
		}
		$this->send(
			$user,
			$this->userNotificationBuilder->walletDebited($amount),
			$channel
		);
	}

	public function sendDepositQueuedNotification(
		User|int $user,
		float $amount,
		array $channel = ['mail', 'database']
	) {
		if (is_integer($user)) {
			$user = User::find($user);
		}
		$this->send(
			$user,
			$this->userNotificationBuilder->depositQueued($amount),
			$channel
		);
	}

	public function sendWithdrawalQueuedNotification(
		User|int $user,
		float $amount,
		array $channel = ['mail', 'database']
	) {
		if (is_integer($user)) {
			$user = User::find($user);
		}
		$this->send(
			$user,
			$this->userNotificationBuilder->withdrawalQueued($amount),
			$channel
		);
	}
}
