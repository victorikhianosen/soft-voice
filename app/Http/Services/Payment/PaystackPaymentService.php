<?php

namespace App\Http\Services\Payment;

use App\Models\Plan;
use Illuminate\Support\Facades\Http;
use App\Http\Interfaces\PaymentGateway;
use App\Http\Interfaces\PaymentGatewayInterface;

class PaystackPaymentService implements PaymentGatewayInterface
{
	protected $client;

	public function __construct()
	{
		$this->client = Http::withHeaders([
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
		$res = $this->client->post('/transaction/initialize', [
			'email'  => request()->user()->email,
			'amount' => $plan->amount * 100,
			'plan'   => $plan->paystack_id,
		]);
		if (!isset($res['data']['authorization_url'])) {
			abort(400, "There was an error getting payment link");
		}

		return $res['data']['authorization_url'];
	}

	public function handleWebhook()
	{
		
	}
}
