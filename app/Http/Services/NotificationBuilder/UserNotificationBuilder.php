<?php

namespace App\Http\Services\NotificationBuilder;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Transaction;

class UserNotificationBuilder
{
	public function invoiceViewed(Invoice $invoice)
	{
		$title = [
			"mail"     => "{$invoice->customer->name} has viewed your invoice - {$invoice->invoice_id}",
			"database" => "<b>{$invoice->customer->name}</b> has viewed your invoice <b>#{$invoice->invoice_id}</b>",
		];

		$body = "Dear {$invoice->user->name},<br><br>Congratulations!<br><br>" .
			    "Your invoice, <b>{$invoice->invoice_id}</b> has been received" .
					"and viewed by {$invoice->customer->name}";
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
		$title = [
			"mail"     => "Overdue Invoice - {$invoice->invoice_id}",
			"database" => "<b>{$invoice->customer->name}'s</b> invoice is overdue <b>#{$invoice->invoice_id}</b>",
		];
		$body = "Dear {$invoice->user->name}! <br><br> Your invoice to {$invoice->customer->name}," .
					"{$invoice->invoice_id} is now overdue. <br> Ensure your invoice is up to date by recording" .
					"the payment or mark the invoice as paid if you have received payment from your customer.";
		$actions = [
			[
				'text' => 'Go to Invoice',
				'url'  => config('env.spa_url') . '/' . $invoice->code . '?action=view',
				'outlined' => false
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function invoicePaymentRecorded(InvoicePayment $payment)
	{
		$method = str_replace('_', ' ', $payment->method->value);
		$title = [
			"mail"     => "Invoice payment recorded - {$payment->invoice->invoice_id}",
			"database" => "<b>{$payment->invoice->customer->name}</b> invoice payment has been recorded <b>#{$payment->invoice->invoice_id}</b>",
		];
		$body = "Dear {$payment->invoice->user->name},<br><br> {$payment->invoice->customer->name} just made a payment of <b>" .
						"{$payment->invoice->currency}" . number_format($payment->amount, 2) . "</b> for invoice {$payment->invoice->invoice_id} via {$method} <br><br>" .
						"Your invoice still has a remaining balance of " . number_format($payment->invoice->amount_due, 2) . "<br><br>" .
						"Total Amount: " . number_format($payment->invoice->amount, 2) ."<br>" .
						"Amount Paid: " . number_format($payment->invoice->amount_paid, 2) . "<br>" .
						"Balance: " . number_format($payment->invoice->amount_due, 2);

		$actions = [
			[
				'text' => 'Go to dashboard',
				'url'  => config('env.spa_url'),
				'outlined' => false
			],
			[
				'text' => 'View as customer',
				'url'  => config('env.spa_url') . '/' . $payment->invoice->code . '?action=view',
				'outlined' => true
			],
		];
		return compact('title', 'body', 'actions');
	}

	public function invoicePaymentReceived(InvoicePayment $payment)
	{
		$title = [
			"mail"     => "Payment received from {$payment->invoice->customer->name} - {$payment->invoice->invoice_id}",
			"database" => "<b>{$payment->invoice->customer->name}</b> invoice payment has been received <b>#{$payment->invoice->invoice_id}</b>",
		];
		$body = "Dear {$payment->invoice->user->name}, <br><br> Your payment of <b> {$payment->invoice->currency}" . number_format($payment->amount, 2) . "</b> for invoice" .
						"{$payment->invoice->invoice_id} has been acknowledged and recorded. <br><br> Your invoice still has a remaining balance of" .
						number_format($payment->invoice->amount_due, 2) . "<br><br>" .
						"Total Amount: " . number_format($payment->invoice->amount, 2) . "<br>" .
						"Amount Paid: " . number_format($payment->invoice->amount_paid, 2) . "<br>" .
						"Balance: " . number_format($payment->invoice->amount_due, 2);
		$actions = [
			[
				'text' => 'Go to wallet',
				'url'  => config('env.spa_url'),
				'outlined' => false
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function invoicePaymentCompleted(Invoice $invoice)
	{
		$title = [
			"mail" => "Payment Completed - {$invoice->invoice_id}",
			"database" => "<b>{$invoice->customer->name}</b> invoice payment has been completed <b>#{$invoice->invoice_id}</b>",
		];
		$body = "Congratulations {$invoice->user->name}! <br><br> You have successfully received full payment for" .
					"invoice {$invoice->invoice_id}. <br><br> Create more invoices to receive more payment.";
		$actions = [
			[
				'text' => 'Go to Dashboard',
				'url'  => config('env.spa_url'),
				'outlined' => false
			]
		];
		return compact('title', 'body', 'actions');
	}

	public function walletCredited(Transaction $transaction)
	{
		$title = "Wallet Credited";
		$body = "Dear {$transaction->user->name},<br><br>" .
			    "Your wallet has been credited with {$transaction->currency} " . number_format($transaction->amount, 2) . ".<br>";
		$actions = [];
		return compact('title', 'body', 'actions');
	}

	public function walletDebited(Transaction $transaction)
	{
		$title = "Wallet Debited";
		$body = "Dear {$transaction->user->name},<br><br>" .
			    "Your wallet has been debited with {$transaction->currency} " . number_format($transaction->amount, 2) . ".<br>";
		$actions = [];
		return compact('title', 'body', 'actions');
	}

	public function depositQueued(Transaction $transaction)
	{
		$title = "Deposit Queued";
		$body = "Dear {$transaction->user->name},<br><br>" .
			    "Your deposit of {$transaction->currency} " . number_format($transaction->amount, 2) .
					" has been queued and will be processed shortly";
		$actions = [];
		return compact('title', 'body', 'actions');
	}

	public function withdrawalQueued(Transaction $transaction)
	{
		$title = "Withdrawal Queued";
		$body = "Dear {$transaction->user->name},<br><br>" .
			    "Your withdrawal of {$transaction->currency} " . number_format($transaction->amount, 2) .
					" has been queued and will be processed shortly";
		$actions = [];
		return compact('title', 'body', 'actions');
	}
}
