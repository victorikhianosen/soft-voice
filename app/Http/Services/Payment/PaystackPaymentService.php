<?php

namespace App\Http\Services\Payment;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;
use App\Enums\PaymentStatus;
use App\Models\OnlinePayment;
use Illuminate\Support\Facades\Http;
use App\Http\Interfaces\PaymentGatewayInterface;
use App\Http\Repositories\SubscriptionRepository;
use App\Http\Repositories\OnlinePaymentRepository;
use App\Http\Repositories\PaymentRepository;
use App\Http\Services\InvoiceService;
use App\Http\Services\NotificationService\UserNotificationService;
use App\Http\Services\WalletService;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Traits\HasRestrictions;

class PaystackPaymentService implements PaymentGatewayInterface
{
	use HasRestrictions;

	const FIXED_FEE = 100;
	const FEE_CAP = 2000;
	const PERCENTAGE_FEE = 1.5 / 100;
	const FEE_WAIVER_THRESHOLD = 2500;

	const CURRENCY = "NGN";

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
			$secret = $user->payStackKey?->secret_key;
			if (!$secret) {
				abort(403, "Payment keys not set for this user");
			}
		} else {
			$secret = config('payment.paystack.secret_key');
		}
		return Http::withHeaders([
			'Authorization' => 'Bearer ' . $secret,
			'Content-Type' => 'application/json',
		])->baseUrl(config('payment.paystack.base_url'));
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
			'gateway'    => 'paystack',
			'model_id'   => request()->user()->id,
		]);

		$res = $this->client()->post('/transaction/initialize', [
			'email'  => request()->user()->email,
			'amount' => $plan->amount_ngn * 100,
			'plan'   => $plan->paystack_id,
			'callback_url' => config('env.spa_url') . "/membership-plans",
			'metadata'   => [
				'type'      => 'subscription',
				'reference' => $payment->reference,
				'auth_type' => 'self'
			],
		]);

		if (!isset($res['data']['authorization_url'])) {
			abort(400, "There was an error getting payment link");
		}

		return $res['data']['authorization_url'];
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
			'gateway'    => 'paystack',
			'model_id'   => $invoice->id,
		]);

		$res = $this->client($data['wallet'] ? null : $invoice->user)->post('/transaction/initialize', [
			'email'  => $invoice->customer->email,
			'amount' => intval(($data['amount'] + $this->getCharge($data['amount'])) * 100),
			'metadata'   => [
				'type'      => 'invoice',
				'invoice_id' => $invoice->id,
				'reference' => $payment->reference,
				'auth_type' => ($data['wallet'] ? 'self' : 'user'),
			],
		]);

		if (!isset($res['data']['authorization_url'])) {
			abort(400, "There was an error getting payment link");
		}

		return $res['data']['authorization_url'];
	}

	public function processWebhook(array $payload)
	{
		$secret = config('payment.paystack.secret_key');

		$type = $payload['data']['metadata']['type'] ?? null;
		$authType = $payload['data']['metadata']['auth_type'] ?? null;

		if ($type === 'invoice' && $authType === 'user') {
			$invoice = Invoice::find($payload['data']['metadata']['invoice_id']);
			$secret = $invoice->user->payStackKey->secret_key;
		}

		if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER)) {
			http_response_code(401);
			exit();
		}

		$input = @file_get_contents("php://input");

		if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, $secret)) {
			http_response_code(401);
			exit();
		}

		logger(json_encode($payload));

		$data = $payload['data'];

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
			// case 'subscription.disable':
			// 	$this->processDisableSubscription($data);
			// 	break;
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


			if ($paid) {
				$lastSubscription = $this->subscriptionRepository->getActive($user->id);

				if ($lastSubscription) {
					$lastSubscription->update([
						'status'  => 'expired',
						'ends_at' => now(),
					]);
				} else {
					$lastSubscription = $this->subscriptionRepository->getLatest([
						'user_id' => $user->id,
						'plan_id' => $plan->id,
						'gateway' => 'paystack',
					]);
				}

				$this->subscriptionRepository->create([
					'user_id'    => $user->id,
					'plan_id'    => $plan->id,
					'starts_at'  => now(),
					'ends_at'    => now()->addMonths($plan->duration),
					'status'     => 'active',
					'gateway'    => 'paystack',
					'gateway_id' => $lastSubscription->gateway_id,
				]);
			}
		} else {
			logger("User or plan not found");
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
					$this->walletService->fundUserWallet($invoice->user->id, $payment->amount, self::CURRENCY);
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
		$charge = ($amount * self::PERCENTAGE_FEE) + self::FIXED_FEE;

		if ($amount < self::FEE_WAIVER_THRESHOLD) {
			$charge -= self::FIXED_FEE;
		}

		if ($charge > self::FEE_CAP) {
			$charge = self::FEE_CAP;
		}

		return $charge;
	}
}
