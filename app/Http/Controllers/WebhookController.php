<?php

namespace App\Http\Controllers;

use App\Http\Services\Payment\PaypalPaymentService;
use App\Http\Services\Payment\PaystackPaymentService;
use App\Http\Services\Payment\StripePaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected StripePaymentService $stripePaymentService,
        protected PaypalPaymentService $paypalPaymentService,
        protected PaystackPaymentService $paystackPaymentService,
    ) {
    }

    public function handlePaystackWebhook(Request $request)
    {
        $this->paystackPaymentService->processWebhook($request->all());
    }

    public function handleStripeWebhook(Request $request)
    {
        $this->stripePaymentService->processWebhook($request->all());
    }

    public function handlePaypalWebhook(Request $request)
    {
        $this->paypalPaymentService->processWebhook($request->all());
    }
}