<?php

namespace App\Http\Services\Payment;

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
use Illuminate\Support\Facades\Http;
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
						'unit_amount_decimal' => ($data['amount'] + $this->getCharge($data['amount'])) * 100,
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
		$event = [];

		$webhookId = config('payment.paystack.webhook_id');

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

		logger($event);

		return http_response_code(200);

		if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER)) {
			http_response_code(401);
			exit();
		}

		$data = $payload['data']['object'];

		switch ($payload['event']) {
			case 'subscription.create':
				$this->processCreateUserSubscription($data['subscription_code'], $data['customer'], $data['plan']);
				break;
			case 'invoice.update':
				$this->processUpdateUserSubscription($data['customer'], $data['plan'], $data['paid']);
				break;
			case 'charge.success':
				$this->processChargeSuccessful($data);
				break;
			case 'subscription.disable':
				$this->processDisableSubscription($data);
				break;
		}

		http_response_code(200);
		exit();
	}

	private function processCreateUserSubscription(string $code, array $customer, array $plan)
	{
		$user = User::where('email', $customer['email'])->first();
		$plan = Plan::where('paystack_id', $plan['plan_code'])->first();
		if ($user && $plan) {
			if ($user->active_subscription) {
				$user->active_subscription->update([
					'ends_at' => now(),
					'status'  => 'cancelled',
				]);
			}
			$this->subscriptionRepository->create([
				'user_id'    => $user->id,
				'plan_id'    => $plan->id,
				'starts_at'  => now(),
				'ends_at'    => now()->addMonths($plan->duration),
				'status'     => 'active',
				'gateway'    => 'paystack',
				'gateway_id' => $code,
			]);
		}
	}

	private function processUpdateUserSubscription(array $customer, array $plan, bool $paid)
	{
		$user = User::where('email', $customer['email'])->first();
		$plan = Plan::where('paystack_id', $plan['plan_code'])->first();
		if ($user && $plan) {
			$this->onlinePaymentRepository->create([
				'type'       => 'subscription_renewal',
				'reference'  => Str::uuid()->toString(),
				'amount'     => $plan->amount_ngn,
				'model_type' => User::class,
				'gateway'    => 'paystack',
				'model_id'   => $user->id,
				'status'     => ($paid ? PaymentStatus::SUCCESS : PaymentStatus::FAILED)
			]);

			$currentSubscription = $this->subscriptionRepository->getActive($user->id);

			$currentSubscription->update([
				'status'  => 'expired',
				'ends_at' => now(),
			]);

			$this->subscriptionRepository->create([
				'user_id'    => $user->id,
				'plan_id'    => $plan->id,
				'starts_at'  => now(),
				'ends_at'    => now()->addMonths($plan->duration),
				'status'     => 'active',
				'gateway'    => 'paystack',
				'gateway_id' => $currentSubscription->gateway_id,
			]);
		}
	}

	private function processChargeSuccessful(array $data)
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
					$this->walletService->fundUserWallet($invoice->user->id, $payment->amount);
				}
			}
		}
	}

	private function processDisableSubscription(array $data): void
	{
		$subscription = Subscription::where('gateway_id', $data['subscription_code'])->first();
		$this->subscriptionRepository->update($subscription, ['status'  => 'expired', 'ends_at' => now()]);

		$plan = Plan::where('slug', 'basic')->first();
		$this->subscriptionRepository->create([
			'user_id'    => $subscription->user->id,
			'plan_id'    => $plan->id,
			'starts_at'  => now(),
			'ends_at'    => now()->addDecade(),
			'status'     => 'active',
		]);
	}

	private function getCharge(float $amount)
	{
		return ($amount * self::PERCENTAGE_FEE) + self::FIXED_FEE;
	}

	public function createOrGetCustomer()
	{
		$user = request()->user();

		if ($user->stripe_id) {
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
