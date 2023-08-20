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

	public function walletDebited(float $amount)
	{
		$title = "Wallet Debited";
		$body = 'Wallet debited content goes here';
		$action = [];
		return compact('title', 'body', 'action');
	}

	public function depositQueued(float $amount)
	{
		$title = "Deposit Queued";
		$body = 'Deposit queued content goes here';
		$action = [];
		return compact('title', 'body', 'action');
	}

	public function withdrawalQueued(float $amount)
	{
		$title = "Withdrawal Queued";
		$body = 'Withdrawal queued content goes here';
		$action = [];
		return compact('title', 'body', 'action');
	}
}
