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
		array $channel = ['mail']
	) {
		$this->send(
			$invoice->customer,
			$this->customerNotificationBuilder->invoiceReminder($invoice),
			$channel
		);
	}

	public function sendInvoiceToCustomer(
        Invoice $invoice,
        array $channel = ['mail']
    ) {
        $this->send(
            $invoice->customer,
            $this->customerNotificationBuilder->invoiceSent($invoice),
            $channel
        );
    }

    public function sendInvoiceOverdueToCustomer(
        Invoice $invoice,
        array $channel = ['mail']
    ) {
        $this->send(
            $invoice->customer,
            $this->customerNotificationBuilder->invoiceOverdue($invoice),
            $channel
        );
    }

    public function sendPaymentToCustomer(
        Invoice $invoice,
        array $channel = ['mail']
    ) {
        $this->send(
            $invoice->customer,
            $this->customerNotificationBuilder->invoicePayment($invoice),
            $channel
        );
    }

	public function sendEstimateToCustomer(
        Estimate $estimate,
        array $channel = ['mail']
    ) {
        $this->send(
            $estimate->customer,
            $this->customerNotificationBuilder->estimateSent($estimate),
            $channel
        );
    }
}
