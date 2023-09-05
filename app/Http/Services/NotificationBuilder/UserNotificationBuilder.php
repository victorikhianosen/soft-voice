<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Invoice;

class UserNotificationBuilder
{
	public function invoiceViewed(Invoice $invoice)
	{
		$title = "Invoice Viewed";
		$body = 'Invoice viewed content goes here';
		$actions = [
			[
				'text' => 'View Invoice',
				'url'  => config('env.spa_url') . 'customer-invoice/' . $invoice->id
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function walletCredited(float $amount)
	{
		$title = "Wallet Credited";
		$body = 'Wallet credited content goes here';
		$actions = [];
		return compact('title', 'body', 'actions');
	}

	public function walletDebited(float $amount)
	{
		$title = "Wallet Debited";
		$body = 'Wallet debited content goes here';
		$actions= [];
		return compact('title', 'body', 'actions');
	}

	public function depositQueued(float $amount)
	{
		$title = "Deposit Queued";
		$body = 'Deposit queued content goes here';
		$actions= [];
		return compact('title', 'body', 'actions');
	}

	public function withdrawalQueued(float $amount)
	{
		$title = "Withdrawal Queued";
		$body = 'Withdrawal queued content goes here';
		$actions = [];
		return compact('title', 'body', 'actions');
	}
}
