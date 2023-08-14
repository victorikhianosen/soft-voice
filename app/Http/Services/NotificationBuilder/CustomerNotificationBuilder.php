<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Estimate;
use App\Models\Invoice;

class CustomerNotificationBuilder
{
	public function invoiceReminder(Invoice $invoice)
	{
		$title = "Invoice Reminder";
		$body = 'This is an Invoice reminder message';
		$action = [
			'text' => 'View Invoice',
			'url'  => config('env.spa_url') . '/' . $invoice->code
		];
		return compact('title', 'body', 'action');
	}

	public function invoiceSent(Invoice $invoice)
	{
		$title = "New Invoice";
		$body = 'Invoice created content goes here';
		$action = [
			'text' => 'View Invoice',
			'url'  => config('env.spa_url') . '/' . $invoice->code
		];
		return compact('title', 'body', 'action');
	}

	public function estimateSent(Estimate $estimate)
	{
		$title = "New Estimate";
		$body = 'Estimate created content goes here';
		$action = [
			'text' => 'View Estimate',
			'url'  => config('env.spa_url') . '/' . $estimate->code
		];
		return compact('title', 'body', 'action');
	}

}
