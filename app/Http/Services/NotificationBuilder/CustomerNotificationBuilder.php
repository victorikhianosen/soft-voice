<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Estimate;
use App\Models\Invoice;

class CustomerNotificationBuilder
{
	public function invoiceReminder(Invoice $invoice)
	{
		$dueDate = \Carbon\Carbon::parse($invoice->due_date);
		$title = "REMINDER: Invoice payment request - ". $invoice->invoice_id;
		$body = "Dear " . $invoice->customer->name . ", you have an\n"
				. "invoice of <b> NGN" . number_format($invoice->amount) . "</b> from <b>" . $invoice->user->name ."</b>.\n"
				."<br>"
				."<br>"
				. "Your invoice will be due for payment in <b>" . $dueDate->diffInDays(now()) . " Days\n</b>"
				. "Which is by ". $dueDate->format('jS \of F, Y');
		$actions = [
			[
				'text' => 'View Invoice',
				'url'  => config('env.spa_url') . '/' . $invoice->code,
				'outlined' => false
			],
			[
				'text' => 'Make Secure Payment',
				'url'  => config('env.spa_url') . '/' . $invoice->code,
				'outlined' => true
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function invoiceSent(Invoice $invoice)
	{
		$dueDate = \Carbon\Carbon::parse($invoice->due_date);
		$title = "New Invoice from ". $invoice->user->name ." - ". $invoice->invoice_id;
		$body = "Dear " . $invoice->customer->name . ", you just received an\n"
				 . "invoice of <b> NGN" . number_format($invoice->amount) . "</b> from <b>" . $invoice->user->name ."</b>.\n"
				 ."<br>"
				 ."<br>"
				 . "Your invoice will be due for payment in <b>" . $dueDate->diffInDays(now()) . " Days\n</b>"
				 . "Which is by ". $dueDate->format('jS \of F, Y');
		$actions = [
			[
				'text' => 'View Invoice Now',
				'url'  => config('env.spa_url') . '/' . $invoice->code,
				'outlined' => false
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function invoiceOverdue(Invoice $invoice)
	{
		$dueDate = \Carbon\Carbon::parse($invoice->due_date);
		$title = "Overdue Invoice - ". $invoice->invoice_id;
		$body = "Dear " . $invoice->customer->name . ", your invoice <b>" . $invoice->invoice_id . "</b> is now overdue."
				 ."<br>"
				 ."<br>"
				 . "Your invoice has been overdue since <b>" . $dueDate->diffInDays(now()) . " Days ago\n</b>"
				 . "Which is at ". $dueDate->format('jS \of F, Y');
		$actions = [
			[
				'text' => 'View Invoice Now',
				'url'  => config('env.spa_url') . '/' . $invoice->code,
				'outlined' => false
			],
			[
				'text' => 'Make Secure Payment',
				'url'  => config('env.spa_url') . '/' . $invoice->code,
				'outlined' => true
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function invoicePayment(Invoice $invoice)
	{
		$title = "Invoice payment receipt from ". $invoice->user->name ." - ". $invoice->invoice_id;
		$body = "Dear " . $invoice->customer->name . ", your payment of <b>NGN " . number_format($invoice->amount) . "</b> has been acknowledged and recorded."
				 ."<br>"
				 ."<br>"
				 . "Please check your updated invoice for confirmation";
		$actions = [
			[
				'text' => 'View Receipt now',
				'url'  => config('env.spa_url') . '/' . $invoice->code,
				'outlined' => false
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function estimateSent(Estimate $estimate)
	{
		$dueDate = \Carbon\Carbon::parse($estimate->due_date);
		$title = "New Estimate from ". $estimate->user->name ." - ". $estimate->invoice_id;
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
			]
		];
		return compact('title', 'body', 'actions');
	}

}
