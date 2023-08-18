<?php

namespace App\Http\Services\NotificationService;

use App\Models\Invoice;
use App\Models\Estimate;
use App\Http\Services\NotificationBuilder\CustomerNotificationBuilder;

class CustomerNotificationService extends BaseNotificationService
{
	public function __construct(
		protected CustomerNotificationBuilder $customerNotificationBuilder,
	) {
	}

	public function sendInvoiceReminderNotification(
		Invoice $invoice,
		$channel = ['mail']
	) {
		$this->send(
			$invoice->customer,
			$this->customerNotificationBuilder->invoiceReminder($invoice),
			$channel
		);
	}

	public function sendInvoiceToCustomer(
        Invoice $invoice, $channel = ['mail']
    ) {
        $this->send(
            $invoice->customer,
            $this->customerNotificationBuilder->invoiceSent($invoice),
            $channel
        );
    }

	public function sendEstimateToCustomer(
        Estimate $estimate, $channel = ['mail']
    ) {
        $this->send(
            $estimate->customer,
            $this->customerNotificationBuilder->estimateSent($estimate),
            $channel
        );
    }
}
