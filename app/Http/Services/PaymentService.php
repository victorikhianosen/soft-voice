<?php

namespace App\Http\Services;

use App\Enums\Currency;
use App\Enums\PaymentGateway;
use App\Http\Interfaces\PaymentGatewayInterface;
use App\Http\Repositories\InvoiceRepository;
use App\Http\Repositories\PlanRepository;
use App\Http\Services\Payment\FlutterwavePaymentService;
use App\Http\Services\Payment\PaystackPaymentService;
use App\Http\Services\Payment\StripePaymentService;
use App\Models\Invoice;

class PaymentService
{
	public function __construct(
		protected PlanRepository $planRepository,
		protected InvoiceRepository $invoiceRepository,
		protected StripePaymentService $stripePaymentService,
		protected PaystackPaymentService $paystackPaymentService,
		protected FlutterwavePaymentService $flutterwavePaymentService,
	) {
	}

	protected function gatewayService(string $gateway): PaymentGatewayInterface
	{
		return match ($gateway) {
			'paystack' => $this->paystackPaymentService,
			'flutterwave' => $this->flutterwavePaymentService,
			'stripe' => $this->stripePaymentService,
		};
	}

	public function generateSubscriptionPayLink(string $gateway, int $planId)
	{
		return $this->gatewayService($gateway)->initializeSubscriptionAndGetPayLink(
			$this->planRepository->find($planId)
		);
	}

	public function generateInvoicePayLink(string $gateway, array $data)
	{
		$invoice = $this->invoiceRepository->find($data['invoice_id']);

		if (!in_array($invoice->currency, Currency::currencies())) {
			abort(400, "Payment not supported for this currency");
		}

		if ($invoice->currency === 'USD' && !in_array($gateway, PaymentGateway::usd())) {
			abort(400, "This gateway is not supported for USD currency");
		}

		if ($invoice->currency === 'NGN' && !in_array($gateway, PaymentGateway::ngn())) {
			abort(400, "This gateway is not supported for NGN currency");
		}

		return $this->gatewayService($gateway)->initializeInvoicePaymentAndGetPayLink($data);
	}
}
