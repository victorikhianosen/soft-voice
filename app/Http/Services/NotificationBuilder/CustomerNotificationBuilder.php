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
		$actions = [
			'text' => 'View Invoice',
			'url'  => config('env.spa_url') . '/' . $invoice->code
		];
		return compact('title', 'body', 'actions');
	}

	public function invoiceSent(Invoice $invoice)
	{
		$dueDate = \Carbon\Carbon::parse($invoice->due_date);
		$logo = $invoice->user->setting['business_logo'];
		$title = "New Invoice";
		$body = "Dear " . $invoice->customer->name . ", you just received an\n"
				 . "invoice of <b> NGN" . number_format($invoice->amount, 2) . "</b> from <b>" . $invoice->user->name ."</b>.\n"
				 ."<br>"
				 ."<br>"
				 . "Your invoice will be due for payment in <b>" . $dueDate->diffInDays(now()) . " Days\n</b>"
				 . "Which is by ". $dueDate->format('jS \of F, Y');
		$actions = [
			'text' => 'View Invoice Now',
			'url'  => config('env.spa_url') . '/' . $invoice->code
		];
		return compact('title', 'body', 'actions');
	}

	public function estimateSent(Estimate $estimate)
	{
		$dueDate = \Carbon\Carbon::parse($estimate->due_date);
		$logo = $estimate->user->setting['business_logo'];
		$title = "New Estimate";
		$body = "Dear " . $estimate->customer->name . ", you just received an\n"
				 . "estimate of <b> NGN" . number_format($estimate->amount, 2) . "</b> from <b>" . $estimate->user->name ."</b>.\n"
				 ."<br>"
				 ."<br>"
				 . "Your estimate will be due for payment in <b>" . $dueDate->diffInDays(now()) . " Days\n</b>"
				 . "Which is by ". $dueDate->format('jS \of F, Y');
		$actions = [
			[
				'text' => 'View Estimate',
				'url'  => config('env.spa_url') . '/' . $estimate->code,
				'outlined' => false
			],
			[
				'text' => 'Make Payment',
				'url'  => config('env.spa_url') . '/' . $estimate->code,
				'outlined' => true
			]
		];
		return compact('title', 'body', 'actions', 'logo');
	}

}
