<?php

namespace App\Http\Services\Payment;

use Stripe\Webhook;
use App\Models\Plan;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Enums\PaymentStatus;
use App\Models\OnlinePayment;
use App\Traits\HasRestrictions;
use App\Http\Services\WalletService;
use Illuminate\Support\Facades\Http;
use App\Http\Services\InvoiceService;
use App\Http\Repositories\PaymentRepository;
use App\Http\Interfaces\PaymentGatewayInterface;
use App\Http\Repositories\SubscriptionRepository;
use App\Http\Repositories\OnlinePaymentRepository;
use App\Http\Services\NotificationService\UserNotificationService;
use Exception;

class PaypalPaymentService implements PaymentGatewayInterface
{
	use HasRestrictions;

	const FIXED_FEE = 0.30;
	const PERCENTAGE_FEE = 2.9 / 100;

	const CURRENCY = "USD";

	protected $client;

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
			$clientId = $user->payPalKey?->client_id;
			$clientSecret = $user->payPalKey?->client_secret;
			if (!$clientId || !$clientSecret) {
				abort(403, "Payment keys not set for this user");
			}
		} else {
			$clientId = config('payment.paypal.client_id');
			$clientSecret = config('payment.paypal.client_secret');
		}
		return Http::withBasicAuth($clientId, $clientSecret)->withHeaders([
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
		])->baseUrl(config('payment.paypal.base_url'));
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
			'gateway'    => 'paypal',
			'model_id'   => request()->user()->id,
		]);

		$data = [
			'plan_id' => $plan->paypal_id,
			'quantity' => '1',
			'subscriber' => [
				'name' => [
					'given_name' => request()->user()->name,
				],
				'email_address' => request()->user()->email,
			],
			'custom_id' => "type=subscription&reference={$payment->reference}&auth_type=self&plan_id={$plan->id}",
			'application_context' => [
				'user_action' => 'SUBSCRIBE_NOW',
				'payment_method' => [
					'payer_selected' => 'PAYPAL',
					'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
				],
				'return_url' => config('env.spa_url') . "/index",
			],
		];


		$response = $this->client()->post('/v1/billing/subscriptions', $data);

		$link = Arr::first(($response['links'] ?? []), fn ($l) => $l['rel'] === 'approve');

		if (!isset($link['href'])) {
			abort(400, "There was an error getting payment link");
		}

		return $link['href'];
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
			'gateway'    => 'paypal',
			'model_id'   => $invoice->id,
		]);

		$authType = $data['wallet'] ? 'self' : 'user';

		$data = [
			'intent' => 'CAPTURE',
			'purchase_units' => [
				[
					'amount' => [
						'currency_code' => self::CURRENCY,
						'value' => round($data['amount'] + $this->getCharge($data['amount']), 2),
					],
					'custom_id' => "type=invoice&invoice_id={$invoice->id}&reference={$payment->reference}&auth_type={$authType}",
				],
			],
			'application_context' => [
				'return_url' => config('env.spa_url') . "/index",
			],
		];

		$response = $this->client()->post('/v2/checkout/orders', $data);

		$link = Arr::first(($response['links'] ?? []), fn ($l) => $l['rel'] === 'approve');

		if (!isset($link['href'])) {
			abort(400, "There was an error getting payment link");
		}

		return $link['href'];
	}

	public function processWebhook(array $payload)
	{
		try {
			$metadata = [];
			if (strpos($payload['event_type'], 'BILLING.SUBSCRIPTION') === 0) {
				$metadataString = $payload['resource']['custom_id'] ?? '';
			} elseif ($payload['event_type'] === 'PAYMENT.SALE.COMPLETED') {
				$metadataString = $payload['resource']['custom'] ?? '';
			} else {
				$metadataString = $payload['resource']['purchase_units'][0]['custom_id'] ?? '';
			}

			foreach (explode('&', $metadataString) as $item) {
				if ($item) {
					[$k, $v] = explode('=', $item);
					$metadata[$k] = $v;
				}
			}

			$webhookId = config('payment.paypal.webhook_id');

			$type = $metadata['type'] ?? null;
			$authType = $metadata['auth_type'] ?? null;

			if ($type === 'invoice' && $authType === 'user') {
				$invoice = Invoice::find($metadata['invoice_id']);
				$webhookId = $invoice->user->payPalKey->webhook_id;
			}

			$headers = apache_request_headers();

			$body = file_get_contents('php://input');

			$data =
				$headers['Paypal-Transmission-Id'] . '|' .
				$headers['Paypal-Transmission-Time'] . '|' .
				$webhookId . '|' . crc32($body);

			$pubKey = openssl_pkey_get_public(file_get_contents($headers['Paypal-Cert-Url']));
			$key = openssl_pkey_get_details($pubKey)['key'];

			$result = openssl_verify(
				$data,
				base64_decode($headers['Paypal-Transmission-Sig']),
				$key,
				'sha256WithRSAEncryption'
			);

			if ($result !== 1) {
				http_response_code(400);
				exit();
			}

			$data = $payload['resource'];

			switch ($payload["event_type"]) {
					case 'BILLING.SUBSCRIPTION.CREATED':
						$this->processCreateUserSubscription($data['id'], $data['subscriber']['email_address'], $data['plan_id']);
						break;
				case 'PAYMENT.SALE.COMPLETED':
					$this->processUpdateUserSubscription($metadata, $data['billing_agreement_id']);
					break;
				case 'CHECKOUT.ORDER.APPROVED':
					$this->processChargeSuccessful($metadata);
					break;
			}
		} catch (Exception $e) {
			logger($e->getMessage());
		}

		http_response_code(200);
		exit();
	}

	private function processUpdateUserSubscription(array $data, string $subscriptionId)
	{
		$payment = OnlinePayment::where('reference', $data['reference'])->first();
		if ($payment && $data['type'] === 'subscription') {
			$user = User::find($payment->model_id);
			$plan = Plan::find($data['plan_id']);
			if ($user && $plan) {
				$this->onlinePaymentRepository->create([
					'type'       => 'subscription',
					'reference'  => Str::uuid()->toString(),
					'amount'     => $plan->amount_ngn,
					'model_type' => User::class,
					'gateway'    => 'paypal',
					'model_id'   => $user->id,
					'status'     => PaymentStatus::SUCCESS,
				]);

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
					'gateway'    => 'paypal',
					'gateway_id' => $subscriptionId,
				]);
			} else {
				logger("User or plan not found");
			}
		}
	}

	private function processCreateUserSubscription(string $code, string $email, string $planId)
	{
		$user = User::where('email', $email)->first();
		$plan = Plan::where('paypal_id', $planId)->first();
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
				'gateway'    => 'paypal',
				'gateway_id' => $code,
			]);
		} else {
			logger("User or plan not found");
		}
	}

	private function processChargeSuccessful(array $metadata)
	{
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
}
