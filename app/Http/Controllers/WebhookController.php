<?php

namespace App\Http\Controllers;

use App\Http\Services\Payment\FlutterwavePaymentService;
use App\Http\Services\Payment\PaystackPaymentService;
use App\Http\Services\Payment\StripePaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected StripePaymentService $stripePaymentService,
        protected PaystackPaymentService $paystackPaymentService,
        protected FlutterwavePaymentService $flutterwavePaymentService,
    ) {
    }

    public function handlePaystackWebhook(Request $request)
    {
        $this->paystackPaymentService->processWebhook($request->all());
    }

    public function handleFlutterwaveWebhook(Request $request)
    {
        $this->flutterwavePaymentService->processWebhook($request->all());
    }

    public function handleStripeWebhook(Request $request)
    {
        $this->stripePaymentService->processWebhook($request->all());
    }
}