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
		if (request()->user()->subscribed()) {
			abort(400, "User already has an active subscription");
		}

		$res = $this->client->post('/transaction/initialize', [
			'email'  => request()->user()->email,
			'amount' => $plan->amount * 100,
			'plan'   => $plan->paystack_id,
			'metadata'   => [
				'type' => 'subscription'
			],
		]);
		
		if (!isset($res['data']['authorization_url'])) {
			abort(400, "There was an error getting payment link");
		}

		return $res['data']['authorization_url'];
	}

	public function processWebhook()
	{
		if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER)) {
			http_response_code(401);
			exit();
		}

		$input = @file_get_contents("php://input");

		// validate event do all at once to avoid timing attack
		if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, config('payment.paystack.secret_key'))) {
			http_response_code(401);
			exit();
		}


		$event = json_decode($input);
		logger(json_encode($event));
		http_response_code(200);

		// http_response_code(400);
		exit();
	}
}
