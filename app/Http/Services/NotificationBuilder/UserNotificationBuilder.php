<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Invoice;

class UserNotificationBuilder
{
	public function invoiceViewed(Invoice $invoice)
	{
		$title = "Invoice Viewed";
		$body = 'Invoice viewed content goes here';
		$action = [
			'text' => 'View Invoice',
			'url'  => config('env.spa_url') . 'customer-invoice/' . $invoice->id
		];
		return compact('title', 'body', 'action');
	}

	public function walletCredited(float $amount)
	{
		$title = "Wallet Credited";
		$body = 'Wallet credited content goes here';
		$action = [];
		return compact('title', 'body', 'action');
	}
}
