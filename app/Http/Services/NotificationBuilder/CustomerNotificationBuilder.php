<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Estimate;
use App\Models\Invoice;

class CustomerNotificationBuilder
{
	public function invoiceReminder(Invoice $invoice)
	{
		$title = "Invoice Reminder";
		$body = 'Invoice reminder content goes here';
		$action = [
			'text' => 'View Invoice',
			'url'  => config('env.spa_url') . 'customer-invoice/' . $invoice->id
		];
		return compact('title', 'body', 'action');
	}

	public function invoiceSMSReminder(Invoice $invoice)
	{
		$body = 'Invoice reminder content goes here';
		return compact('body');
	}

	public function invoiceWhatsappReminder(Invoice $invoice)
	{
		$code = "4367";
		$body = "*$code* is your verification code. For your security, do not share this code.";
		return compact('body');
	}

	public function invoiceSent(Invoice $invoice)
	{
		$title = "New Invoice";
		$body = 'Invoice created content goes here';
		$action = [
			'text' => 'View Invoice',
			'url'  => config('env.spa_url') . 'customer-invoice/' . $invoice->id
		];
		return compact('title', 'body', 'action');
	}

	public function estimateSent(Estimate $estimate)
	{
		$title = "New Estimate";
		$body = 'Estimate created content goes here';
		$action = [
			'text' => 'View Estimate',
			'url'  => config('env.spa_url') . 'customer-estimate/' . $estimate->id
		];
		return compact('title', 'body', 'action');
	}

}
