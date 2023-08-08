<?php

namespace App\Http\Controllers;

use App\Http\Services\Payment\PaystackPaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected PaystackPaymentService $paystackPaymentService
    ) {
    }

    public function handlePaystackWebook()
    {
        $this->paystackPaymentService->processWebhook();
    }
}
