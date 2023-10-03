<?php

namespace App\Http\Services\Payment;

use App\Enums\PaymentGateway;
use Stripe\Webhook;
use App\Models\Plan;
use App\Models\User;
use App\Models\Invoice;
use Stripe\StripeClient;
use Illuminate\Support\Str;
use App\Enums\PaymentStatus;
use App\Models\Subscription;
use App\Models\OnlinePayment;
use App\Traits\HasRestrictions;
use App\Http\Services\WalletService;
use App\Http\Services\InvoiceService;
use App\Http\Repositories\PaymentRepository;
use App\Http\Interfaces\PaymentGatewayInterface;
use App\Http\Repositories\SubscriptionRepository;
use App\Http\Repositories\OnlinePaymentRepository;
use Stripe\Exception\SignatureVerificationException;
use App\Http\Services\NotificationService\UserNotificationService;

class StripePaymentService implements PaymentGatewayInterface
{
	use HasRestrictions;

	const FIXED_FEE = 0.30;
	const PERCENTAGE_FEE = 2.9 / 100;

	const CURRENCY = "USD";

	public function __construct(
		protected WalletService $walletService,
		protected InvoiceService $invoiceService,
		protected PaymentRepository $paymentRepository,
		protected SubscriptionRepository $subscriptionRepository,
		protected OnlinePaymentRepository $onlinePaymentRepository,
		protected UserNotificationService $userNotificationService,
	) {
	}

	public function client(User|null $user = null)
	{
		if ($user) {
			$secret = $user->stripeKey?->secret_key;
			if (!$secret) {
				abort(403, "Payment keys not set for this user");
			}
		} else {
			$secret = config('payment.stripe.secret_key');
		}
		return new StripeClient($secret);
	}

	/**
	 * Creates a subscription and returns link to complete payment
	 *
	 * @param Plan $plan
	 * @return string
	 */
	public function initializeSubscriptionAndGetPayLink(Plan $plan): string
	{
		if (request()->user()->subscribedToPaidPlan()) {
			abort(400, "User already has an active subscription");
		}

		$payment = $this->onlinePaymentRepository->create([
			'type'       => 'subscription',
			'reference'  => Str::uuid()->toString(),
			'amount'     => $plan->amount_ngn,
			'model_type' => User::class,
			'gateway'    => 'stripe',
			'model_id'   => request()->user()->id,
		]);

		$res = $this->client()->checkout->sessions->create([
			'success_url' => config('env.spa_url') . "/index",
			'line_items' => [
				[
					'price' => $plan->stripe_id,
					'quantity' => 1,
				],
			],
			'currency' => 'USD',
			'mode' => 'subscription',
			'customer' => $this->createOrGetCustomer(),
			'metadata'   => [
				'type'      => 'subscription',
				'reference' => $payment->reference,
				'auth_type' => 'self'
			],
		]);

		if (!isset($res['url'])) {
			abort(400, "There was an error getting payment link");
		}

		return $res['url'];
	}

	public function initializeInvoicePaymentAndGetPayLink(array $data): string
	{
		$invoice = Invoice::find($data['invoice_id']);

		if ($this->isRestricted('in-app-payments', $invoice->user)) {
			abort(400, "You current plan doesn't support this feature");
		}

		if (!$data['wallet'] && $this->isRestricted('custom-payment', $invoice->user)) {
			abort(400, "You current plan doesn't support this feature");
		}

		$payment = $this->onlinePaymentRepository->create([
			'type'       => 'invoice_payment',
			'reference'  => Str::uuid()->toString(),
			'amount'     => $data['amount'],
			'model_type' => Invoice::class,
			'gateway'    => 'stripe',
			'model_id'   => $invoice->id,
		]);

		$res = $this->client()->checkout->sessions->create([
			'success_url' => config('env.spa_url') . "/index",
			'line_items' => [
				[
					'price_data' => [
						'currency' => 'USD',
						'unit_amount_decimal' => intval(($data['amount'] + $this->getCharge($data['amount'])) * 100),
						'product_data' => [
							'name' => "Payment for invoice {$invoice->invoice_id}",
						],
					],
					'quantity' => 1
				],
			],
			'currency' => 'USD',
			'mode' => 'payment',
			'customer_email'  => $invoice->customer->email,
			'metadata'   => [
				'type'      => 'invoice',
				'invoice_id' => $invoice->id,
				'reference' => $payment->reference,
				'auth_type' => ($data['wallet'] ? 'self' : 'user'),
			],
		]);

		if (!isset($res['url'])) {
			abort(400, "There was an error getting payment link");
		}

		return $res['url'];
	}

	public function processWebhook(array $payload)
	{
		$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

		$webhookId = config('payment.stripe.webhook_id');

		$metadata = $payload['data']['object']['metadata'];
		$type = $metadata['type'] ?? null;
		$authType = $metadata['auth_type'] ?? null;

		if ($type === 'invoice' && $authType === 'user') {
			$invoice = Invoice::find($metadata['invoice_id']);
			$webhookId = $invoice->user->stripeKey->webhook_id;
		}

		try {
			$event = Webhook::constructEvent(
				@file_get_contents('php://input'),
				$sig_header,
				$webhookId
			);
		} catch (\UnexpectedValueException $e) {
			http_response_code(400);
			exit();
		} catch (SignatureVerificationException $e) {
			http_response_code(400);
			exit();
		}

		logger(json_encode($payload));

		$data = $event['data']['object'];

		switch ($payload['type']) {
			case 'invoice.updated':
				$email = $data['customer_email'];
				$plan = $data['lines']['data'][0]['plan']['id'];
				$subscriptionId = $data['lines']['data'][0]['subscription'];
				$paid = $data['paid'];
				$this->processUpdateUserSubscription($email, $plan, $subscriptionId, $paid);
				break;
			case 'checkout.session.completed':
				$this->processChargeSuccessful($data);
				break;
		}

		http_response_code(200);
		exit();
	}

	private function processUpdateUserSubscription(
		string $email,
		string $planId,
		string $subscriptionId,
		bool $paid
	) {
		$user = User::where('email', $email)->first();
		$plan = Plan::where('stripe_id', $planId)->first();
		if ($user && $plan) {
			$this->onlinePaymentRepository->create([
				'type'       => 'subscription',
				'reference'  => Str::uuid()->toString(),
				'amount'     => $plan->amount_ngn,
				'model_type' => User::class,
				'gateway'    => 'stripe',
				'model_id'   => $user->id,
				'status'     => ($paid ? PaymentStatus::SUCCESS : PaymentStatus::FAILED)
			]);

			if ($paid) {
				$currentSubscription = $this->subscriptionRepository->getActive($user->id);

				if ($currentSubscription) {
					$currentSubscription->update([
						'status'  => 'expired',
						'ends_at' => now(),
					]);
				}

				$this->subscriptionRepository->create([
					'user_id'    => $user->id,
					'plan_id'    => $plan->id,
					'starts_at'  => now(),
					'ends_at'    => now()->addMonths($plan->duration),
					'status'     => 'active',
					'gateway'    => 'stripe',
					'gateway_id' => $subscriptionId,
				]);
			}
		} else {
			logger("User or plan not found");
		}
	}

	private function processChargeSuccessful($data)
	{
		$metadata = $data['metadata'];
		$payment = OnlinePayment::where('reference', $metadata['reference'])->where('status', PaymentStatus::PENDING)->first();
		if ($payment) {
			$this->onlinePaymentRepository->update($payment, ['status' => PaymentStatus::SUCCESS]);

			if ($metadata['type'] === 'invoice' && $payment->model_type === Invoice::class) {
				$invoice = Invoice::find($payment->model_id);
				$invoicePayment = $this->paymentRepository->create([
					'invoice_id'    => $invoice->id,
					'date'          => now()->format('Y-m-d'),
					'method'        => 'card',
					'amount'        => $payment->amount,
					'notify_client' => true,
				]);

				$this->invoiceService->updateInvoiceStatusByPayments($invoice->id);
				$this->userNotificationService->sendInvoicePaymentReceivedNotification($invoicePayment);

				if ($metadata['auth_type'] === 'self') {
					$this->walletService->fundUserWallet($invoice->user->id, $payment->amount, self::CURRENCY);
				}
			}
		}
	}

	private function getCharge(float $amount)
	{
		return ($amount * self::PERCENTAGE_FEE) + self::FIXED_FEE;
	}

	public function createOrGetCustomer()
	{
		$user = request()->user();

		if ($user->stripe_customer_id) {
			return $user->stripe_customer_id;
		} else {
			$id = $this->client()->customers->create([
				'name' => $user->name,
				'email' => $user->email,
			])['id'];

			$user->stripe_customer_id = $id;
			$user->save();
			return $id;
		}
	}
}
