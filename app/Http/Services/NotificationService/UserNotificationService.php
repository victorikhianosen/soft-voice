<?php

namespace App\Http\Services\NotificationService;

use App\Models\Invoice;
use App\Http\Services\NotificationBuilder\UserNotificationBuilder;
use App\Models\InvoicePayment;
use App\Models\Transaction;

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
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$invoice->user,
			$this->userNotificationBuilder->invoiceOverdue($invoice),
			$channel
		);
	}

	public function sendInvoicePaymentRecordedNotification(
		InvoicePayment $payment,
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$payment->invoice->user,
			$this->userNotificationBuilder->invoicePaymentRecorded($payment),
			$channel
		);
	}

	public function sendInvoicePaymentReceivedNotification(
		InvoicePayment $payment,
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$payment->invoice->user,
			$this->userNotificationBuilder->invoicePaymentReceived($payment),
			$channel
		);
	}

	public function sendInvoicePaymentCompletedNotification(
		Invoice $invoice,
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$invoice->user,
			$this->userNotificationBuilder->invoicePaymentCompleted($invoice),
			$channel
		);
	}

	public function sendWalletCreditNotification(
		Transaction $transaction,
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$transaction->user,
			$this->userNotificationBuilder->walletCredited($transaction),
			$channel
		);
	}

	public function sendWalletDebitNotification(
		Transaction $transaction,
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$transaction->user,
			$this->userNotificationBuilder->walletDebited($transaction),
			$channel
		);
	}

	public function sendDepositQueuedNotification(
		Transaction $transaction,
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$transaction->user,
			$this->userNotificationBuilder->depositQueued($transaction),
			$channel
		);
	}

	public function sendWithdrawalQueuedNotification(
		Transaction $transaction,
		array $channel = ['mail', 'database']
	) {
		$this->send(
			$transaction->user,
			$this->userNotificationBuilder->withdrawalQueued($transaction),
			$channel
		);
	}
}
