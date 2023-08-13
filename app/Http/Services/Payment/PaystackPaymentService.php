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
use App\Models\Subscription;

class PaystackPaymentService implements PaymentGatewayInterface
{
	public function __construct(
		protected SubscriptionRepository $subscriptionRepository,
		protected OnlinePaymentRepository $onlinePaymentRepository
	) {
	}

	public function client(User|null $user = null)
	{
		return Http::withHeaders([
			'Authorization' => 'Bearer ' . config('payment.paystack.secret_key'),
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
		if (request()->user()->subscribed()) {
			abort(400, "User already has an active subscription");
		}

		$payment = $this->onlinePaymentRepository->create([
			'type'       => 'subscription',
			'reference'  => Str::uuid()->toString(),
			'amount'     => $plan->amount,
			'model_type' => User::class,
			'gateway'    => 'paystack',
			'model_id'   => request()->user()->id,
		]);

		$res = $this->client()->post('/transaction/initialize', [
			'email'  => request()->user()->email,
			'amount' => $plan->amount * 100,
			'plan'   => $plan->paystack_id,
			'metadata'   => [
				'type'      => 'subscription',
				'reference' => $payment->reference,
			],
		]);

		if (!isset($res['data']['authorization_url'])) {
			abort(400, "There was an error getting payment link");
		}

		return $res['data']['authorization_url'];
	}

	public function processWebhook(array $payload)
	{
		if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER)) {
			http_response_code(401);
			exit();
		}

		$input = @file_get_contents("php://input");

		if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, config('payment.paystack.secret_key'))) {
			http_response_code(401);
			exit();
		}

		$data = $payload['data'];

		switch ($payload['event']) {
			case 'subscription.create':
				$this->processCreateUserSubscription($data['subscription_code'], $data['customer'], $data['plan']);
				break;
			case 'invoice.update':
				$this->processUpdateUserSubscription($data['customer'], $data['plan'], $data['paid']);
				break;
			case 'charge.success':
				$payment = OnlinePayment::where('reference', $data['metadata']['reference'])->first();
				if ($payment) {
					$this->onlinePaymentRepository->update($payment, ['status' => PaymentStatus::SUCCESS]);
				}
				break;
			case 'subscription.disable':
				$subscription = Subscription::where('gateway_id', $data['subscription_code'])->first();
				$this->subscriptionRepository->update($subscription, [
					'status'  => 'completed',
					'ends_at' => now()
				]);
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
			$this->subscriptionRepository->create([
				'user_id'    => $user->id,
				'plan_id'    => $plan->id,
				'starts_at'  => now(),
				'ends_at'    => now()->addMonth(),
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
				'amount'     => $plan->amount,
				'model_type' => User::class,
				'gateway'    => 'paystack',
				'model_id'   => $user->id,
				'status'     => ($paid ? PaymentStatus::SUCCESS : PaymentStatus::FAILED)
			]);
			$currentSubscription = $this->subscriptionRepository->getActive($user->id);
			$currentSubscription->ends_at = now()->addMonth();
			$currentSubscription->save();
		}
	}
}
