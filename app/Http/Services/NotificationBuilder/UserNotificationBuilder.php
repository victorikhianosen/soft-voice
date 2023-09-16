<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Invoice;

class UserNotificationBuilder
{
	public function invoiceViewed(Invoice $invoice)
	{
		$title = $invoice->customer->name ." has viewd your invoice ". "- ". $invoice->invoice_id ;
		$body = "Dear " . $invoice->user->name . ","
				."<br>"
				."<br>"
				."Congratulations!"
				."<br>"
				."<br>"
				. "Your invoice, invoice <b>" .$invoice->code.  "</b> has been received and viewed by ". $invoice->customer->name;
		$actions = [
			[
				'text' => 'Go to dashboard',
				'url'  => config('env.spa_url') . '/index',
				'outlined' => false
			],
			[
				'text' => 'View as customer',
				'url'  => config('env.spa_url') . '?action=view',
				'outlined' => true
			],
		];
		return compact('title', 'body', 'actions');
	}

	public function invoiceOverdue(Invoice $invoice)
	{
		$title = "Overdue Invoice - ". $invoice->invoice_id;
		$body = "Dear " . $invoice->user->name . "!"
				 ."<br>"
				 ."<br>"
				 ."Your invoice to ". $invoice->customer->name .", ". $invoice->invoice_id ." is now overdue."
				 ."<br>"
				 ."<br>"
				 . "Ensure your invoice is up to date by recording the payment or mark the invoice as paid if you have received payment from your customer.";
		$actions = [
			[
				'text' => 'Go to Invoice',
				'url'  => config('env.spa_url') . '/' . $invoice->code . '?action=view',
				'outlined' => false
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function invoicePayementRecorded(Invoice $invoice)
	{
		$title = "Invoice payment recorded - ". $invoice->invoice_id;
		$body = "Dear " . $invoice->user->name . ","
				 ."<br>"
				 ."<br>"
				 .$invoice->customer->name. " just made a payment of <b>NGN". number_format($invoice->amount, 2) ."</b> for invoice ". $invoice->invoice_id ." via (payment gateway)"//. $invoice->payment_gateway
				 ."<br>"
				 ."<br>"
				 . "Your invoice still has a remaining balance of ". number_format($invoice->amount, 2)
				 ."<br>"
				 ."<br>"
				 ."Total Amount: ". number_format($invoice->amount, 2)
				 ."<br>"
				 ."Amount Paid: ". number_format($invoice->amount, 2)
				 ."<br>"
				 ."Balance: ". number_format($invoice->amount, 2);
		$actions = [
			[
				'text' => 'Go to dashboard',
				'url'  => config('env.spa_url'),
				'outlined' => false
			],
			[
				'text' => 'View as customer',
				'url'  => config('env.spa_url') . '/' . $invoice->code . '?action=view',
				'outlined' => true
			],
		];
		return compact('title', 'body', 'actions');
	}

	public function invoicePayementReceived(Invoice $invoice)
	{
		$title = "Payment received from ". $invoice->customer->name ." - ". $invoice->invoice_id;
		$body = "Dear " . $invoice->user->name . ","
				 ."<br>"
				 ."<br>"
				 ."Your payment of <b> NGN". number_format($invoice->amount, 2) ."</b> for invoice ". $invoice->invoice_id ." has been acknowledged and recorded."
				 ."<br>"
				 ."<br>"
				 . "Your invoice still has a remaining balance of ". number_format($invoice->amount, 2)
				 ."<br>"
				 ."<br>"
				 ."Total Amount: ". number_format($invoice->amount, 2)
				 ."<br>"
				 ."Amount Paid: ". number_format($invoice->amount, 2)
				 ."<br>"
				 ."Balance: ". number_format($invoice->amount, 2);
		$actions = [
			[
				'text' => 'Go to wallet',
				'url'  => config('env.spa_url'),
				'outlined' => false
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function invoicePayementCompleted(Invoice $invoice)
	{
		$dueDate = \Carbon\Carbon::parse($invoice->due_date);
		$title = "Payment Completed - ". $invoice->invoice_id;
		$body = "Congratulations " . $invoice->user->name . "!"
				 ."<br>"
				 ."<br>"
				 ."You have successfully received full payment for invoice ". $invoice->invoice_id ."."
				 ."<br>"
				 ."<br>"
				 ."Create more invoices to receive more payment. ";
		$actions = [
			[
				'text' => 'Go to Dashboard',
				'url'  => config('env.spa_url'),
				'outlined' => false
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
